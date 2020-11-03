<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Models\Test;
use App\Queries\TestsQueryBuilder;
use Illuminate\Support\Collection;

/**
 * Class TestsRepository
 *
 * @package App\Repositories
 */
final class TestsRepository implements TestsRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function get(int $id): Test
    {
        /** @var Test $test */
        $test = $this->query()->getById($id);
        return $test;
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
    public function create(array $data): Test
    {
        $test = new Test();
        $test->compactFill($data);
        $this->save($test);
        return $test;
    }

    /**
     * @inheritDoc
     */
    public function save(Test $test): void
    {
        $test->save();
    }

    /**
     * @inheritDoc
     */
    public function update(array $data, Test $test): void
    {
        $test->compactFill($data);
        $this->save($test);
    }

    /**
     * @inheritDoc
     */
    public function delete(Test $test): void
    {
        if ($test->deleted_at) {
            $test->forceDelete();
        } else {
            $test->delete();
        }
    }

    /**
     * @inheritDoc
     */
    public function restore(Test $test): void
    {
        $test->restore();
        $this->save($test);
    }

    /**
     * @inheritDoc
     */
    public function query(): TestsQueryBuilder
    {
        return (new TestsQueryBuilder(new Test()));
    }
}
