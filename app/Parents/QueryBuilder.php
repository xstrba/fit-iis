<?php declare(strict_types=1);

namespace App\Parents;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as FWQueryBuilder;
use Illuminate\Support\Collection;

/**
 * Class QueryBuilder
 *
 * @package App\Parents
 */
abstract class QueryBuilder extends Builder
{
    /**
     * QueryBuilder constructor.
     *
     * @param \App\Parents\Model $model
     * @return void
     */
    public function __construct(Model $model)
    {
        $connection = $model->getConnection();

        parent::__construct(new FWQueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        ));
        $this->setModel($model);
    }

    /**
     * Get model by id or throw exception
     *
     * @param $id
     * @return \App\Parents\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById($id): Model
    {
        /** @var \App\Parents\Model $model */
        $model = $this->findOrFail($id);
        return $model;
    }

    /**
     * Find models by ids
     *
     * @param $ids
     * @return \Illuminate\Support\Collection
     */
    public function findByIds($ids): Collection
    {
        return $this->findMany($ids);
    }

    /**
     * Get models by ids or throw exception
     *
     * @param $ids
     * @return \Illuminate\Support\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getByIds($ids): Collection
    {
        return $this->findOrFail($ids);
    }

    /**
     * Get first model
     *
     * @return \App\Parents\Model|null
     */
    public function getFirst(): ?Model
    {
        /** @var \App\Parents\Model|null $model */
        $model = $this->first();
        return $model;
    }

    /**
     * Get first model or throw exception
     *
     * @return \App\Parents\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getFirstOrFail(): Model
    {
        /** @var \App\Parents\Model $model */
        $model = $this->firstOrFail();
        return $model;
    }

    /**
     * Find model by id
     *
     * @param $id
     * @return \App\Parents\Model|null
     */
    public function findById($id): ?Model
    {
        /** @var \App\Parents\Model|null $model */
        $model = $this->find($id);
        return $model;
    }
}
