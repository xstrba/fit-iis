<?php declare(strict_types=1);

namespace App\Queries;

use App\Models\Test;
use App\Parents\QueryBuilder;

/**
 * Class TestsQueryBuilder
 *
 * @package App\Queries
 */
final class TestsQueryBuilder extends QueryBuilder
{
    /**
     * @param string $name
     * @return \App\Queries\TestsQueryBuilder
     */
    public function whereName(string $name): TestsQueryBuilder
    {
        return $this->where(Test::ATTR_NAME, $name);
    }
}
