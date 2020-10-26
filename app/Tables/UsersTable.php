<?php declare(strict_types=1);

namespace App\Tables;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Models\User;
use App\Parents\Table;
use App\Queries\UsersQueryBuilder;
use App\Tables\Columns\Column;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        \Illuminate\Contracts\Translation\Translator $translator,
        \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator,
        UsersRepositoryInterface $usersRepository
    ) {
        $this->usersRepository = $usersRepository;
        parent::__construct($translator, $urlGenerator);
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
            Column::init(User::ATTR_NICKNAME, $this->translator->get('labels.' . User::ATTR_NICKNAME))
                ->sortable()
                ->centered()
                ->searchable()
        );

        $this->addColumn(
            Column::init(User::ATTR_CREATED_AT, $this->translator->get('labels.' . User::ATTR_CREATED_AT))
                ->sortable()
                ->right()
        );
    }

    /**
     * @param \App\Models\User $user
     * @return mixed[]
     */
    private function modelToArray(User $user): array
    {
        return [
            User::ATTR_ID => $user->getKey(),
            User::ATTR_FIRST_NAME => $user->first_name,
            User::ATTR_LAST_NAME => $user->last_name,
            User::ATTR_NICKNAME => $user->nickname,
            User::ATTR_CREATED_AT => $user->created_at->format('d. m. Y H:i:s'),
        ];
    }
}
