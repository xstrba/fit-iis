<?php declare(strict_types=1);

namespace App\Queries;

use App\Models\Group;
use App\Parents\QueryBuilder;

/**
 * Class GroupsQueryBuilder
 *
 * @package App\Queries
 */
final class GroupsQueryBuilder extends QueryBuilder
{
    /**
     * @param string $name
     * @return \App\Queries\GroupsQueryBuilder
     */
    public function whereName(string $name): GroupsQueryBuilder
    {
        return $this->where(Group::ATTR_NAME, $name);
    }
}
