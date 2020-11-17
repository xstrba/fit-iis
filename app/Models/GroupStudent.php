<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;

/**
 * Class GroupStudent
 *
 * @property int $group_id
 * @property int $student_id
 * @property bool $finished
 * @property \App\Models\Group $group
 * @property \App\Models\User $student
 * @package App\Models
 */
final class GroupStudent extends Model
{
    public const ATTR_GROUP_ID = 'group_id';
    public const ATTR_STUDENT_ID = 'student_id';
    public const ATTR_FINISHED = 'finished';

    public const RELATION_GROUP = 'group';
    public const RELATION_STUDENT = 'student';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_GROUP_ID,
        self::ATTR_STUDENT_ID,
        self::ATTR_FINISHED,
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_GROUP_ID => 'int',
        self::ATTR_STUDENT_ID => 'int',
        self::ATTR_FINISHED => 'bool',
    ];

    /**
     * Test instance belongs to one group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Group::class, self::ATTR_GROUP_ID);
    }

    /**
     * Test instance belongs to one user aka student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, self::ATTR_STUDENT_ID);
    }
}
