<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\GroupsRepositoryInterface;
use App\Models\Group;
use App\Queries\GroupsQueryBuilder;
use Illuminate\Support\Collection;

/**
 * Class GroupsRepository
 *
 * @package App\Repositories
 */
final class GroupsRepository implements GroupsRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function get(int $id): Group
    {
        /** @var Group $group */
        $group = $this->query()->getById($id);
        return $group;
    }

    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return $this->query()->get();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Group
    {
        $group = new Group();
        $group->compactFill($data);
        $this->save($group);
        return $group;
    }

    /**
     * @inheritDoc
     */
    public function save(Group $group): void
    {
        $group->save();
    }

    /**
     * @inheritDoc
     */
    public function update(array $data, Group $group): void
    {
        $group->compactFill($data);
        $this->save($group);
    }

    /**
     * @inheritDoc
     */
    public function delete(Group $group): void
    {
        $group->forceDelete();
    }

    /**
     * @inheritDoc
     */
    public function query(): GroupsQueryBuilder
    {
        return (new GroupsQueryBuilder(new Group()));
    }
}
