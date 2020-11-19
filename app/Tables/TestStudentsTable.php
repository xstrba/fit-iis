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

/**
 * Class TestStudentsTable
 *
 * @package App\Tables
 */
final class TestStudentsTable extends Table
{
    private const ROUTE_PARAM_TEST_ID = 'id';
    private const COLUMN_POINTS = 'points';

    /**
     * @var int $perPage
     */
    protected int $perPage = 30;

    /**
     * @var string|null $defaultSort
     */
    protected ?string $defaultSort = null;

    /**
     * @var string $defaultSortDirection
     */
    protected string $defaultSortDirection = 'desc';

    /**
     * @var \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     */
    private TestsRepositoryInterface $testsRepository;

    /**
     * @var \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    private UsersRepositoryInterface $usersRepository;

    /**
     * @var \App\Models\Test $test
     */
    private Test $test;

    /**
     * Table constructor.
     *
     * @param \Illuminate\Contracts\Translation\Translator $translator
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     * @param \App\Services\AuthService $authService
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        Translator $translator,
        UrlGenerator $urlGenerator,
        AuthService $authService,
        TestsRepositoryInterface $testsRepository,
        UsersRepositoryInterface $usersRepository
    ) {
        $this->defaultSort = TestStudent::table() . '.' .TestStudent::ATTR_ACCEPTED;
        $this->testsRepository = $testsRepository;
        $this->usersRepository = $usersRepository;
        parent::__construct($translator, $urlGenerator, $authService);
    }

    /**
     * Initialize table
     *
     * @param \App\Models\Test $test
     * @return \App\Parents\Table
     */
    public function initializeTable(Test $test): Table
    {
        $this->test = $test;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBaseApiUrl(): string
    {
        return $this->urlGenerator->route('tests.students.json.index', $this->test->id);
    }

    /**
     * @inheritDoc
     */
    public function getData(Request $request): LengthAwarePaginator
    {
        $query = $this->test->students()->whereNull(User::table() . '.' . User::ATTR_DELETED_AT);

        $this->applyFilters($request, $query);

        foreach ($this->getSortColumns($request) as $sortColumn) {
            $query->orderBy($sortColumn[0], $sortColumn[1] ?? 'asc');
        }

        $searchColumns = $this->getSearchColumns();
        $searchInput = $request->input(self::QUERY_PARAM_SEARCH);
        if ($searchInput && \count($searchColumns)) {
            $query->where(static function (UsersQueryBuilder $query) use ($searchColumns, $searchInput): void {
                foreach ($searchColumns as $column) {
                    $query->orWhere($column, 'LIKE', '%' . $searchInput . '%');
                }
            });
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $paginated */
        $paginated = $query->paginate($this->perPage);
        $paginated->getCollection()->transform(function (User $user): array {
            return $this->modelToArray($user);
        });
        return $paginated;
    }

    /**
     * @inheritDoc
     */
    protected function initializeColumns(): void
    {
        $this->addColumn(
            Column::init(User::ATTR_NICKNAME, $this->translator->get('labels.' . User::ATTR_NICKNAME))
                ->sortable()
                ->searchable()
        );

        $this->addColumn(
            Column::init(TestStudent::table() . '.' .TestStudent::ATTR_ACCEPTED, $this->translator->get('labels.accepted'))
                ->sortable()
                ->centered()
                ->width('150px')
        );

        $this->addColumn(
            Column::init(self::COLUMN_POINTS, $this->translator->get('labels.points'))
                ->centered()
                ->width('150px')
        );

        $this->addActionsColumn();
    }

    /**
     *  @inheritDoc
     */
    protected function initializeFilters(): void
    {
        $itemsFilter = new Filter('filter_items', '');
        $itemsFilter->addOptions([
            FilterOption::init(null, $this->translator->get('labels.all_items')),
            FilterOption::init('0', $this->translator->get('labels.nonaccepted')),
            FilterOption::init('1', $this->translator->get('labels.accepted')),
        ]);

        $this->addFilter($itemsFilter, static function ($query, $value) {
            if ($value === '1') {
                $query->where(TestStudent::table() . '.' .TestStudent::ATTR_ACCEPTED, true);
            }

            if ($value === '0') {
                $query->where(TestStudent::table() . '.' .TestStudent::ATTR_ACCEPTED, false);
            }

            return $query;
        });
    }

    /**
     * @param \App\Models\User $user
     * @return mixed[]
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function modelToArray(User $user): array
    {
        $points = '0/0';
        $auth = $this->authService->user();
        if (
            $auth->role >= RolesEnum::ROLE_PROFESSOR ||
            $this->test->assistants->where('pivot.' . TestAssistant::ATTR_ACCEPTED, true)->contains($auth->id)
        ) {
            /** @var GroupStudent|null $groupSolution */
            $groupSolution = $user->testSolutions()->whereIn(
                GroupStudent::ATTR_GROUP_ID,
                $this->test->groups->pluck(Group::ATTR_ID)->toArray()
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
        } else {
            $points = '';
        }

        return \array_merge($user->toArray(), [
            TestStudent::table() . '.' .TestStudent::ATTR_ACCEPTED => $user->pivot->getAttribute(TestStudent::ATTR_ACCEPTED),
            self::FIELD_ACTIONS => $this->getActions($user),
            self::COLUMN_POINTS => $points,
        ]);
    }

    /**
     * @param \App\Models\User $user
     * @return mixed[]
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    private function getActions(User $user): array
    {
        $actions = [];
        $auth = $this->authService->user();

        $approved = $this->test->students->filter(static function (User $student): bool {
            return (bool)$student->pivot->getAttributeValue(TestStudent::ATTR_ACCEPTED);
        });

        if (
            $auth->can(PermissionsEnum::ACCEPT_STUDENT, $this->test) &&
            !$approved->contains($user->id)
        ) {
            $actions[] = $this->getActionData(
                'check',
                $this->translator->get('labels.accept_student'),
                $this->urlGenerator->route('tests.accept-student', [$this->test->id, $user->id]),
                'post'
            );
        }

        if ($auth->can(PermissionsEnum::ACCEPT_STUDENT, $this->test)) {
            $actions[] = $this->getActionData(
                'times',
                $this->translator->get('labels.remove_student'),
                $this->urlGenerator->route('tests.remove-student', [$this->test->id, $user->id]),
                'post'
            );

            /** @var GroupStudent|null $solution */
            $solution = $user->testSolutions()->whereIn(
                GroupStudent::ATTR_GROUP_ID,
                $this->test->groups->pluck(Group::ATTR_ID)->toArray(),
            )->where(GroupStudent::ATTR_FINISHED, true)->first();
            if ($solution) {
                $actions[] = $this->getActionData(
                    'check',
                    $this->translator->get('labels.evaluate'),
                    $this->urlGenerator->route('tests.solution.users', [$this->test->id, $user->id])
                );
            }
        }

        return $actions;
    }
}
