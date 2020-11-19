<?php declare(strict_types=1);

namespace App\Tables;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Question;
use App\Models\QuestionStudent;
use App\Models\Test;
use App\Models\TestAssistant;
use App\Models\TestStudent;
use App\Models\User;
use App\Parents\Table;
use App\Queries\TestsQueryBuilder;
use App\Queries\UsersQueryBuilder;
use App\Services\AuthService;
use App\Tables\Columns\Column;
use App\Tables\Filters\Filter;
use App\Tables\Filters\FilterOption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Class MyTestsTable
 *
 * @package App\Tables
 */
final class MyTestsTable extends TestsTable
{
    /**
     * @inheritDoc
     */
    public function getBaseApiUrl(): string
    {
        return $this->urlGenerator->route('tests.my.json.index');
    }

    /**
     * @inheritDoc
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getData(Request $request): LengthAwarePaginator
    {
        $query = $this->testsRepository->query();
        $user = $this->authService->user();

        $query->where(static function (TestsQueryBuilder $query) use ($user): void {
             if ($user->role >= RolesEnum::ROLE_PROFESSOR) {
                 $query->orWhereHas(Test::RELATION_PROFESSOR, static function (UsersQueryBuilder $query) use ($user): void {
                        $query->where(User::table() . '.' . User::ATTR_ID, $user->id);
                 });
             }

            if ($user->role >= RolesEnum::ROLE_ASSISTANT) {
                $query->orWhereHas(Test::RELATION_ASSISTANTS, static function (UsersQueryBuilder $query) use ($user): void {
                    $query->where(User::table() . '.' . User::ATTR_ID, $user->id);
                });
            }

            if ($user->role < RolesEnum::ROLE_ASSISTANT) {
                $query->orWhereHas(Test::RELATION_STUDENTS, static function (UsersQueryBuilder $query) use ($user): void {
                    $query->where(User::table() . '.' . User::ATTR_ID, $user->id);
                });
            }
        });

        $this->applyFilters($request, $query);

        foreach ($this->getSortColumns($request) as $sortColumn) {
            $query->orderBy($sortColumn[0], $sortColumn[1] ?? 'asc');
        }

        $searchColumns = $this->getSearchColumns();
        $searchInput = $request->input(self::QUERY_PARAM_SEARCH);
        if ($searchInput && \count($searchColumns)) {
            $query->where(static function (TestsQueryBuilder $query) use ($searchColumns, $searchInput): void {
                foreach ($searchColumns as $column) {
                    $query->orWhere($column, 'LIKE', '%' . $searchInput . '%');
                }
            });
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginated */
        $paginated = $query->paginate($this->perPage);
        $paginated->getCollection()->transform(function (Test $test): array {
            return $this->modelToArray($test);
        });
        return $paginated;
    }

    /**
     * @inheritDoc
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function initializeColumns(): void
    {
        $this->addColumn(
            Column::init(Test::ATTR_SUBJECT, $this->translator->get('labels.' . Test::ATTR_SUBJECT))
                ->sortable()
                ->searchable()
        );

        $this->addColumn(
            Column::init(Test::ATTR_NAME, $this->translator->get('labels.title'))
                ->sortable()
                ->searchable()
        );

        $this->addColumn(
            Column::init(Test::RELATION_PROFESSOR, $this->translator->get('labels.' . Test::RELATION_PROFESSOR))
        );

        $this->addColumn(
            Column::init(Test::ATTR_START_DATE, $this->translator->get('labels.' . 'start'))
                ->sortable()
                ->right()
                ->width("180px")
        );

        $this->addColumn(
            Column::init(Test::ATTR_CREATED_AT, $this->translator->get('labels.' . Test::ATTR_CREATED_AT))
                ->sortable()
                ->right()
                ->width("180px")
        );

        if ($this->authService->user()->role < RolesEnum::ROLE_ASSISTANT) {
            $this->addColumn(
                Column::init('points', $this->translator->get('labels.' . 'points'))
                    ->centered()
                    ->width("180px")
            );
        }

        $this->addActionsColumn();
    }

    /**
     * @param \App\Models\Test $test
     * @return mixed[]
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function modelToArray(Test $test): array
    {
        $user = $this->authService->user();
        $points = '0/0';

        /** @var GroupStudent|null $groupSolution */
        $groupSolution = $user->testSolutions()->whereIn(
            GroupStudent::ATTR_GROUP_ID,
            $test->groups->pluck(Group::ATTR_ID)->toArray()
        )->where(GroupStudent::ATTR_FINISHED, true)->first();

        if ($groupSolution) {
            $questionSolution = $user->questionSolutions()->whereIn(
                QuestionStudent::ATTR_QUESTION_ID,
                $groupSolution->group->questions->pluck(Question::ATTR_ID)->toArray(),
            )->get();
            $gotPoints = $questionSolution->sum(QuestionStudent::ATTR_POINTS);
            $maxPoints = $questionSolution->sum(static function (QuestionStudent $questionStudent): int {
                return $questionStudent->question->max_points;
            });

            $points = $gotPoints . ' / ' . $maxPoints;
        }

        return \array_merge($test->toArray(), [
            Test::ATTR_CREATED_AT => $test->created_at->format('d. m. Y H:i:s'),
            self::FIELD_ACTIONS => $this->getActions($test),
            Test::ATTR_START_DATE => $test->start_date->format('d. m. Y H:i:s'),
            'points' => $points,
            Test::RELATION_PROFESSOR => $test->professor->name
        ]);
    }

    /**
     * @param \App\Models\Test $test
     * @return mixed[]
     */
    private function getActions(Test $test): array
    {
        $auth = $this->authService->tryUser();
        $actions = [];
        if (!$auth) {
            return $actions;
        }

        if ($auth->can(PermissionsEnum::SHOW, $test)) {
            $actions[] = $this->getActionData(
                'eye',
                $this->translator->get('labels.show'),
                $this->urlGenerator->route('tests.show', $test->getKey())
            );
        }

        if ($auth->can(PermissionsEnum::EDIT, $test)) {
            $actions[] = $this->getActionData(
                'edit',
                $this->translator->get('labels.edit'),
                $this->urlGenerator->route('tests.edit', $test->getKey())
            );

            if ($test->deleted_at) {
                $actions[] = $this->getActionData(
                    'trash-restore',
                    $this->translator->get('labels.restore'),
                    $this->urlGenerator->route('tests.restore', $test->getKey()),
                    'post',
                );
            }
        }

        if ($auth->can(PermissionsEnum::DELETE, $test)) {
            $actions[] = $this->getActionData(
                'trash-alt',
                $this->translator->get('labels.delete'),
                $this->urlGenerator->route('tests.destroy', $test->getKey()),
                'delete'
            );
        }

        return $actions;
    }
}
