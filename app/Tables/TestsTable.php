<?php declare(strict_types=1);

namespace App\Tables;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
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
 * Class TestsTable
 *
 * @package App\Tables
 */
class TestsTable extends Table
{
    /**
     * @var int $perPage
     */
    protected int $perPage = 30;

    /**
     * @var string|null $defaultSort
     */
    protected ?string $defaultSort = Test::ATTR_CREATED_AT;

    /**
     * @var string $defaultSortDirection
     */
    protected string $defaultSortDirection = 'desc';

    /**
     * @var \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     */
    protected TestsRepositoryInterface $testsRepository;

    /**
     * @var \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    protected UsersRepositoryInterface $usersRepository;

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
        $this->testsRepository = $testsRepository;
        $this->usersRepository = $usersRepository;
        parent::__construct($translator, $urlGenerator, $authService);
    }

    /**
     * @inheritDoc
     */
    public function getBaseApiUrl(): string
    {
        return $this->urlGenerator->route('tests.json.index');
    }

    /**
     * @inheritDoc
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getData(Request $request): LengthAwarePaginator
    {
        $query = $this->testsRepository->query();

        if ($this->authService->user()->role < RolesEnum::ROLE_ASSISTANT) {
            $query->where(Test::ATTR_START_DATE, '>', Carbon::now());
        }

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

        $this->addActionsColumn();
    }

    /**
     * @inheritDoc
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function initializeFilters(): void
    {
        $itemsFilter = new Filter('filter_items', '');
        $user = $this->authService->user();

        $options = [FilterOption::init(null, $this->translator->get('labels.all_items'))];
        if (
            $this->authService->user()->role >= RolesEnum::ROLE_ASSISTANT &&
            $this->testsRepository->query()->whereNotNull(Test::ATTR_DELETED_AT)->count()
        ) {
            $options[] = FilterOption::init('deleted', $this->translator->get('labels.deleted'));
        }
        $itemsFilter->addOptions($options);
        $this->addFilter($itemsFilter, static function (TestsQueryBuilder $query, $value): TestsQueryBuilder {
            if ($value === 'deleted') {
                return $query->whereNotNull(Test::ATTR_DELETED_AT);
            }

            return $query->whereNull(Test::ATTR_DELETED_AT);
        });

        // subjects filter
        $subjectsFilter = new Filter('filter_subjects', $this->translator->get('labels.filter_subjects'));
        $subjectsFilter->setMultiple();

        $subjectOptions = [];
        $subjects = $this->testsRepository->query()
            ->select(Test::ATTR_SUBJECT)
            ->distinct()
            ->get()
            ->pluck(Test::ATTR_SUBJECT)
            ->toArray();
        foreach ($subjects as $subject) {
            $subjectOptions[] = FilterOption::init($subject, $subject);
        }
        $subjectsFilter->addOptions($subjectOptions);
        $this->addFilter($subjectsFilter, static function (TestsQueryBuilder $query, ?string $value = null): TestsQueryBuilder {
            if ($value) {
                return $query->whereIn(Test::ATTR_SUBJECT, \explode(self::QUERY_FILTER_VALUE_DELIMITER, $value));
            }

            return $query;
        });

        // professors filter
        $professorsFilter = new Filter('filter_professors', $this->translator->get('labels.filter_professors'));
        $professorsFilter->setMultiple();

        $professorIds = $this->testsRepository
            ->query()
            ->select(Test::ATTR_PROFESSOR_ID)
            ->distinct()
            ->get()
            ->pluck(Test::ATTR_PROFESSOR_ID)
            ->toArray();

        $profOptions = [];
        $profs = $this->usersRepository
            ->query()
            ->whereIn(User::ATTR_ID, $professorIds)
            ->whereRole(RolesEnum::ROLE_PROFESSOR, '>=')
            ->get();

        foreach ($profs as $prof) {
            $profOptions[] = FilterOption::init($prof->id, $prof->name);
        }
        $professorsFilter->addOptions($profOptions);
        $this->addFilter($professorsFilter, static function (TestsQueryBuilder $query, ?string $value = null): TestsQueryBuilder {
            if ($value) {
                return $query->whereIn(Test::ATTR_PROFESSOR_ID, \explode(self::QUERY_FILTER_VALUE_DELIMITER, $value));
            }

            return $query;
        });

        // my all assistant
        $rolesFilter = new Filter('filter_test_role', $this->translator->get('labels.filter_test_role'));
        $rolesOptions = [FilterOption::init(null, $this->translator->get('labels.all_items')),];
        if ($user->role >= RolesEnum::ROLE_PROFESSOR) {
            $rolesOptions[] =  FilterOption::init('prof', $this->translator->get('labels.i_am_professor'));
        }

        if ($user->role >= RolesEnum::ROLE_PROFESSOR) {
            $rolesOptions[] =  FilterOption::init('prof', $this->translator->get('labels.i_am_professor'));
        }

        if ($user->role >= RolesEnum::ROLE_ASSISTANT) {
            $rolesOptions[] =  FilterOption::init('assis', $this->translator->get('labels.i_am_assistant'));
            $rolesOptions[] = FilterOption::init('assis_req', $this->translator->get('labels.i_have_assistant_request'));
        }

        if ($user->role < RolesEnum::ROLE_ASSISTANT) {
            $rolesOptions[] =  FilterOption::init('student', $this->translator->get('labels.i_am_student'));
            $rolesOptions[] = FilterOption::init('student_req', $this->translator->get('labels.i_have_student_request'));
        }

        $rolesFilter->addOptions($rolesOptions);

        $this->addFilter($rolesFilter, function (TestsQueryBuilder $query, ?string $value = null): TestsQueryBuilder {
            if ($value === 'prof') {
                return $query->where(Test::ATTR_PROFESSOR_ID, $this->authService->getId());
            }

            if ($value === 'assis') {
                return $query->whereHas(Test::RELATION_ASSISTANTS, function (UsersQueryBuilder $subQuery): void {
                    $subQuery->where(TestAssistant::table() . '.' . TestAssistant::ATTR_ACCEPTED, true)
                        ->where(TestAssistant::ATTR_ASSISTANT_ID, $this->authService->getId());
                });
            }

            if ($value === 'assis_req') {
                return $query->whereHas(Test::RELATION_ASSISTANTS, function (UsersQueryBuilder $subQuery): void {
                    $subQuery->where(TestAssistant::table() . '.' . TestAssistant::ATTR_ACCEPTED, false)
                        ->where(TestAssistant::ATTR_ASSISTANT_ID, $this->authService->getId());
                });
            }

            if ($value === 'student') {
                return $query->whereHas(Test::RELATION_STUDENTS, function (UsersQueryBuilder $subQuery): void {
                    $subQuery->where(TestStudent::table() . '.' . TestStudent::ATTR_ACCEPTED, true)
                        ->where(TestStudent::ATTR_STUDENT_ID, $this->authService->getId());
                });
            }

            if ($value === 'student_req') {
                return $query->whereHas(Test::RELATION_STUDENTS, function (UsersQueryBuilder $subQuery): void {
                    $subQuery->where(TestStudent::table() . '.' . TestStudent::ATTR_ACCEPTED, false)
                        ->where(TestStudent::ATTR_STUDENT_ID, $this->authService->getId());
                });
            }
            return $query;
        });
    }

    /**
     * @param \App\Models\Test $test
     * @return mixed[]
     */
    private function modelToArray(Test $test): array
    {
        return \array_merge($test->toArray(), [
            Test::ATTR_CREATED_AT => $test->created_at->format('d. m. Y H:i:s'),
            self::FIELD_ACTIONS => $this->getActions($test),
            Test::ATTR_START_DATE => $test->start_date->format('d. m. Y H:i:s'),
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

        if ($auth->can(PermissionsEnum::REQUEST_ASSISTANT, $test)) {
            $actions[] = $this->getActionData(
                'check',
                $this->translator->get('labels.assistant_request'),
                $this->urlGenerator->route('tests.request-assistant', $test->getKey()),
                'post'
            );
        }

        if ($auth->role < RolesEnum::ROLE_ASSISTANT && $auth->can(PermissionsEnum::REQUEST_STUDENT, $test)) {
            $actions[] = $this->getActionData(
                'check',
                $this->translator->get('labels.request_student'),
                $this->urlGenerator->route('tests.request-student', $test->getKey()),
                'post'
            );
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
