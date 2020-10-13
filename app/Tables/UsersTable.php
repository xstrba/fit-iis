<?php declare(strict_types=1);

namespace App\Tables;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Models\User;
use App\Parents\Table;
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
            $query->sortByColumn($sortColumn[0], $sortColumn[1] ?? 'asc');
        }

        return $query->paginate($this->perPage);
    }

    /**
     * @inheritDoc
     */
    protected function initializeColumns(): void
    {
        $this->addColumn(
            Column::init(User::ATTR_FIRST_NAME, $this->translator->get('labels.' . User::ATTR_FIRST_NAME))
                ->sortable()
        );

        $this->addColumn(
            Column::init(User::ATTR_LAST_NAME, $this->translator->get('labels.' . User::ATTR_LAST_NAME))
                ->sortable()
        );

        $this->addColumn(
            Column::init(User::ATTR_NICKNAME, $this->translator->get('labels.' . User::ATTR_NICKNAME))
                ->sortable()
                ->centered()
        );

        $this->addColumn(
            Column::init(User::ATTR_CREATED_AT, $this->translator->get('labels.' . User::ATTR_CREATED_AT))
                ->sortable()
                ->right()
        );
    }
}
