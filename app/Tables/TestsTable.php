<?php declare(strict_types=1);

namespace App\Tables;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Models\Test;
use App\Parents\Table;
use App\Queries\TestsQueryBuilder;
use App\Services\AuthService;
use App\Tables\Columns\Column;
use App\Tables\Filters\Filter;
use App\Tables\Filters\FilterOption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;

/**
 * Class TestsTable
 *
 * @package App\Tables
 */
final class TestsTable extends Table
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
    private TestsRepositoryInterface $testsRepository;

    /**
     * Table constructor.
     *
     * @param \Illuminate\Contracts\Translation\Translator $translator
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     * @param \App\Services\AuthService $authService
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     */
    public function __construct(
        Translator $translator,
        UrlGenerator $urlGenerator,
        AuthService $authService,
        TestsRepositoryInterface $testsRepository
    ) {
        $this->testsRepository = $testsRepository;
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
     */
    public function getData(Request $request): LengthAwarePaginator
    {
        $query = $this->testsRepository->query();

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
            Column::init(Test::ATTR_NAME, $this->translator->get('labels.' . Test::ATTR_NAME))
                ->sortable()
                ->searchable()
        );

        $this->addColumn(
            Column::init(Test::ATTR_START_DATE, $this->translator->get('labels.' . Test::ATTR_START_DATE))
                ->sortable()
                ->searchable()
        );

        $this->addColumn(
            Column::init(Test::ATTR_CREATED_AT, $this->translator->get('labels.' . Test::ATTR_CREATED_AT))
                ->sortable()
                ->right()
                ->width("200px")
        );

        $this->addActionsColumn();
    }

    /**
     *  @inheritDoc
     */
    protected function initializeFilters(): void
    {
        $itemsFilter = new Filter('filter_items', '');

        $options = [FilterOption::init(null, $this->translator->get('labels.all_items'))];
        if ($this->testsRepository->query()->whereNotNull(Test::ATTR_DELETED_AT)->count()) {
            $options[] = FilterOption::init('deleted', $this->translator->get('labels.deleted'));
        }
        $itemsFilter->addOptions($options);
        $this->addFilter($itemsFilter, static function (TestsQueryBuilder $query, $value): TestsQueryBuilder {
            if ($value === 'deleted') {
                return $query->whereNotNull(Test::ATTR_DELETED_AT);
            }

            return $query->whereNull(Test::ATTR_DELETED_AT);
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
                $this->translator->get('pages.profile'),
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
