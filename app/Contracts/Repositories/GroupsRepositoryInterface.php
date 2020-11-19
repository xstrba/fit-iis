<?php declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Queries\GroupsQueryBuilder;
use Illuminate\Support\Collection;
use App\Models\Group;

/**
 * Interface GroupsRepositoryInterface
 *
 * @package App\Contracts\Repositories
 */
interface GroupsRepositoryInterface
{
    /**
     * Get model by id or throw exception
     *
     * @param int $id
     * @return \App\Models\Group
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get(int $id): Group;

    /**
     * Get all items
     *
     * @return \Illuminate\Support\Collection|\App\Models\Group[]
     */
    public function getAll(): Collection;

    /**
     * Create new item
     *
     * @param mixed[] $data
     * @return \App\Models\Group
     */
    public function create(array $data): Group;

    /**
     * Save item to database
     *
     * @param \App\Models\Group $group
     * @return void
     */
    public function save(Group $group): void;

    /**
     * Update item
     *
     * @param mixed[] $data
     * @param \App\Models\Group $group
     * @return void
     */
    public function update(array $data, Group $group): void;

    /**
     * Delete item from database
     *
     * @param \App\Models\Group $group
     *
     * @return void
     * @throws \Exception
     */
    public function delete(Group $group): void;

    /**
     * Get query builder for model
     *
     * @return \App\Queries\GroupsQueryBuilder
     */
    public function query(): GroupsQueryBuilder;
}
