<?php declare(strict_types=1);

namespace App\Tables;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\User;
use App\Parents\Table;
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
 * Class UsersTable
 *
 * @package App\Tables
 */
final class UsersTable extends Table
{
    /**
     * @var int $perPage
     */
    protected int $perPage = 30;

    /**
     * @var string|null $defaultSort
     */
    protected ?string $defaultSort = User::ATTR_CREATED_AT;

    /**
     * @var string $defaultSortDirection
     */
    protected string $defaultSortDirection = 'desc';

    /**
     * @var \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    private UsersRepositoryInterface $usersRepository;

    /**
     * Table constructor.
     *
     * @param \Illuminate\Contracts\Translation\Translator $translator
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     * @param \App\Services\AuthService $authService
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        Translator $translator,
        UrlGenerator $urlGenerator,
        AuthService $authService,
        UsersRepositoryInterface $usersRepository
    ) {
        $this->usersRepository = $usersRepository;
        parent::__construct($translator, $urlGenerator, $authService);
    }

    /**
     * @inheritDoc
     */
    public function getBaseApiUrl(): string
    {
        return $this->urlGenerator->route('users.json.index');
    }

    /**
     * @inheritDoc
     */
    public function getData(Request $request): LengthAwarePaginator
    {
        $query = $this->usersRepository->query();

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
            Column::init(User::ATTR_FIRST_NAME, $this->translator->get('labels.' . User::ATTR_FIRST_NAME))
                ->sortable()
                ->searchable()
        );

        $this->addColumn(
            Column::init(User::ATTR_LAST_NAME, $this->translator->get('labels.' . User::ATTR_LAST_NAME))
                ->sortable()
                ->searchable()
        );

        $this->addColumn(
            Column::init(User::ATTR_EMAIL, $this->translator->get('labels.' . User::ATTR_EMAIL))
                ->sortable()
                ->searchable()
        );

        $this->addColumn(
            Column::init(User::ATTR_BIRTH, $this->translator->get('labels.' . 'birth_date'))
                ->sortable()
                ->width("150px")
        );

        $this->addColumn(
            Column::init(User::ATTR_CREATED_AT, $this->translator->get('labels.' . User::ATTR_CREATED_AT))
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
        if ($this->usersRepository->query()->whereNotNull(User::ATTR_DELETED_AT)->count()) {
            $options[] = FilterOption::init('deleted', $this->translator->get('labels.deleted'));
        }
        $itemsFilter->addOptions($options);
        $this->addFilter($itemsFilter, static function (UsersQueryBuilder $query, $value): UsersQueryBuilder {
            if ($value === 'deleted') {
                return $query->whereNotNull(User::ATTR_DELETED_AT);
            }

            return $query->whereNull(User::ATTR_DELETED_AT);
        });

        // roles filter
        $rolesFilter = new Filter('roles_filter', $this->translator->get('labels.filter_roles'));
        $rolesFilter->setMultiple();

        $rolesOptions = [];
        foreach (RolesEnum::instance()->getValues() as $value) {
            $rolesOptions[] = FilterOption::init($value, $this->translator->get('roles.' . $value));
        }
        $rolesFilter->addOptions($rolesOptions);
        $this->addFilter($rolesFilter, static function (UsersQueryBuilder $query, ?string $value = null): UsersQueryBuilder {
            if (!$value) {
                return $query;
            }

            return $query->whereIn(User::ATTR_ROLE, \explode(self::QUERY_FILTER_VALUE_DELIMITER, $value));
        });
    }

    /**
     * @param \App\Models\User $user
     * @return mixed[]
     */
    private function modelToArray(User $user): array
    {
        return \array_merge($user->toArray(), [
            User::ATTR_CREATED_AT => $user->created_at->format('d. m. Y H:i:s'),
            User::ATTR_BIRTH => $user->birth->format('d. m. Y'),
            self::FIELD_ACTIONS => $this->getActions($user),
        ]);
    }

    /**
     * @param \App\Models\User $user
     * @return mixed[]
     */
    private function getActions(User $user): array
    {
        $auth = $this->authService->tryUser();
        $actions = [];
        if (!$auth) {
            return $actions;
        }

        if ($auth->can(PermissionsEnum::SHOW, $user)) {
            $actions[] = $this->getActionData(
                'eye',
                $this->translator->get('pages.profile'),
                $this->urlGenerator->route('users.show', $user->getKey())
            );
        }

        if ($auth->can(PermissionsEnum::EDIT, $user)) {
            $actions[] = $this->getActionData(
                'edit',
                $this->translator->get('labels.edit'),
                $this->urlGenerator->route('users.edit', $user->getKey())
            );

            if ($user->deleted_at) {
                $actions[] = $this->getActionData(
                    'trash-restore',
                    $this->translator->get('labels.restore'),
                    $this->urlGenerator->route('users.restore', $user->getKey()),
                    'post',
                );
            }
        }

        if ($auth->can(PermissionsEnum::DELETE, $user)) {
            $actions[] = $this->getActionData(
                'trash-alt',
                $this->translator->get('labels.delete'),
                $this->urlGenerator->route('users.destroy', $user->getKey()),
                'delete'
            );
        }

        return $actions;
    }
}
