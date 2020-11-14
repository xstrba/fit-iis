<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;

/**
 * Class Question
 *
 * @property int $group_id
 * @property string $name
 * @property string|null $text
 * @property int $type
 * @property int $files_number
 * @property int $min_points
 * @property int $max_points
 * @property \App\Models\Group $group
 * @package App\Models
 */
final class Question extends Model
{
    public const ATTR_GROUP_ID = 'group_id';
    public const ATTR_NAME = 'name';
    public const ATTR_TEXT = 'text';
    public const ATTR_TYPE = 'type';
    public const ATTR_FILES_NUMBER = 'files_number';
    public const ATTR_MIN_POINTS = 'min_points';
    public const ATTR_MAX_POINTS = 'max_points';

    public const RELATION_GROUP = 'group';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_GROUP_ID,
        self::ATTR_NAME,
        self::ATTR_TEXT,
        self::ATTR_TYPE,
        self::ATTR_FILES_NUMBER,
        self::ATTR_MIN_POINTS,
        self::ATTR_MAX_POINTS,
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_GROUP_ID => 'int',
        self::ATTR_NAME => 'string',
        self::ATTR_TEXT => 'string',
        self::ATTR_TYPE => 'int',
        self::ATTR_FILES_NUMBER => 'int',
        self::ATTR_MIN_POINTS => 'int',
        self::ATTR_MAX_POINTS => 'int',
    ];

    /**
     * Question belongs to test group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class, self::ATTR_GROUP_ID);
    }
}
