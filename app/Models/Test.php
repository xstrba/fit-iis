<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;
use App\Queries\TestsQueryBuilder;

/**
 * Class Test
 *
 * @property int $professor_id
 * @property string $subject
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $start_date
 * @property int $time_limit
 * @property int $questions_number
 * @property \App\Models\User $professor
 * @property \Illuminate\Support\Collection|\App\Models\User[] $assistants
 * @property \Illuminate\Support\Collection|\App\Models\Group[] $groups
 * @package App\Models
 */
final class Test extends Model
{
    public const ATTR_PROFESSOR_ID = 'professor_id';
    public const ATTR_SUBJECT = 'subject';
    public const ATTR_NAME = 'name';
    public const ATTR_DESCRIPTION = 'description';
    public const ATTR_START_DATE = 'start_date';
    public const ATTR_TIME_LIMIT = 'time_limit';
    public const ATTR_QUESTIONS_NUMBER = 'questions_number';

    public const RELATION_PROFESSOR = 'professor';
    public const RELATION_ASSISTANTS = 'assistants';
    public const RELATION_GROUPS = 'groups';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_PROFESSOR_ID,
        self::ATTR_SUBJECT,
        self::ATTR_NAME,
        self::ATTR_DESCRIPTION,
        self::ATTR_START_DATE,
        self::ATTR_TIME_LIMIT,
        self::ATTR_QUESTIONS_NUMBER,
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_PROFESSOR_ID => 'int',
        self::ATTR_SUBJECT => 'string',
        self::ATTR_NAME => 'string',
        self::ATTR_DESCRIPTION => 'string',
        self::ATTR_START_DATE => 'datetime:Y-m-d H:i:s',
        self::ATTR_TIME_LIMIT => 'int',
        self::ATTR_QUESTIONS_NUMBER => 'int',
    ];

    /**
     * @return \App\Models\Test|\App\Queries\TestsQueryBuilder|\Illuminate\Database\Eloquent\Builder
     */
    public function newModelQuery()
    {
        return new TestsQueryBuilder(new self());
    }

    /**
     * Test belongs to professor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function professor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, self::ATTR_PROFESSOR_ID);
    }

    /**
     * Test has one assistant
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assistants(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            TestAssistant::class,
            TestAssistant::ATTR_TEST_ID,
            TestAssistant::ATTR_ASSISTANT_ID
        )->withPivot(TestAssistant::ATTR_ACCEPTED)
            ->orderBy(TestAssistant::table() . '.' . TestAssistant::ATTR_ACCEPTED, 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Group::class, Group::ATTR_TEST_ID);
    }

    /**
     * Test is valid if has at least one group and each group has
     * at least the amount of questions specified in questions number
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->groups->count() < 1) {
            return false;
        }

        foreach ($this->groups as $group) {
            if ($group->questions->count() < $this->questions_number) {
                return false;
            }
        }

        return true;
    }
}
