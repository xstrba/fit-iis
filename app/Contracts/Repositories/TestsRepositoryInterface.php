<?php declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Queries\TestsQueryBuilder;
use Illuminate\Support\Collection;
use App\Models\Test;

/**
 * Interface TestsRepositoryInterface
 *
 * @package App\Contracts\Repositories
 */
interface TestsRepositoryInterface
{
    /**
     * Get model by id or throw exception
     *
     * @param int $id
     * @return \App\Models\Test
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function get(int $id): Test;

    /**
     * Get all items
     *
     * @return \Illuminate\Support\Collection|\App\Models\Test[]
     */
    public function getAll(): Collection;

    /**
     * Create new item
     *
     * @param mixed[] $data
     * @return \App\Models\Test
     */
    public function create(array $data): Test;

    /**
     * Save item to database
     *
     * @param \App\Models\Test $test
     * @return void
     */
    public function save(Test $test): void;

    /**
     * Update item
     *
     * @param mixed[] $data
     * @param \App\Models\Test $test
     * @return void
     */
    public function update(array $data, Test $test): void;

    /**
     * Delete item from database
     *
     * @param \App\Models\Test $test
     *
     * @return void
     * @throws \Exception
     */
    public function delete(Test $test): void;

    /**
     * @param \App\Models\Test $test
     */
    public function restore(Test $test): void;

    /**
     * Get query builder for model
     *
     * @return \App\Queries\TestsQueryBuilder
     */
    public function query(): TestsQueryBuilder;
}
