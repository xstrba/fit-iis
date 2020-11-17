<?php declare(strict_types=1);

namespace App\Queries;

use App\Models\File;
use App\Parents\QueryBuilder;

/**
 * Class FileQueryBuilder
 *
 * @package App\Queries
 */
final class FileQueryBuilder extends QueryBuilder
{
    /**
     * @param string $path
     * @return \App\Queries\FileQueryBuilder
     */
    public function wherePath(string $path): FileQueryBuilder
    {
        return $this->where(File::ATTR_PATH, $path);
    }
}
