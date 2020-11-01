<?php declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Queries\UsersQueryBuilder;
use Illuminate\Support\Collection;
use App\Models\User;

/**
 * Interface UsersRepositoryInterface
 *
 * @package App\Contracts\Repositories
 */
interface UsersRepositoryInterface
{
    /**
     * Get model by id or throw exception
     *
     * @param int $id
     * @return \App\Models\User
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get(int $id): User;

    /**
     * Get all items
     *
     * @return \Illuminate\Support\Collection|\App\Models\User[]
     */
    public function getAll(): Collection;

    /**
     * Create new item
     *
     * @param mixed[] $data
     * @return \App\Models\User
     */
    public function create(array $data): User;

    /**
     * Save item to database
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function save(User $user): void;

    /**
     * Update item
     *
     * @param mixed[] $data
     * @param \App\Models\User $user
     * @return void
     */
    public function update(array $data, User $user): void;

    /**
     * Delete item from database
     *
     * @param \App\Models\User $user
     *
     * @return void
     * @throws \Exception
     */
    public function delete(User $user): void;

    /**
     * @param \App\Models\User $user
     */
    public function restore(User $user): void;

    /**
     * Find item by email
     *
     * @param string $email
     * @return \App\Models\User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find item by nickname
     *
     * @param string $nickname
     * @return \App\Models\User|null
     */
    public function findByNickname(string $nickname): ?User;

    /**
     * Get query builder for model
     *
     * @return \App\Queries\UsersQueryBuilder
     */
    public function query(): UsersQueryBuilder;
}
