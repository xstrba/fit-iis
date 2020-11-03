<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;
use App\Queries\TestsQueryBuilder;

/**
 * Class Test
 *
 * @property int $professor_id
 * @property int|null $assistant_id
 * @property string $subject
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $start_date
 * @property int $time_limit
 * @property int $questions_number
 * @property \App\Models\User $professor
 * @property \App\Models\User|null $assistant
 * @package App\Models
 */
final class Test extends Model
{
    public const ATTR_PROFESSOR_ID = 'professor_id';
    public const ATTR_ASSISTANT_ID = 'assistant_id';
    public const ATTR_SUBJECT = 'subject';
    public const ATTR_NAME = 'name';
    public const ATTR_DESCRIPTION = 'description';
    public const ATTR_START_DATE = 'start_date';
    public const ATTR_TIME_LIMIT = 'time_limit';
    public const ATTR_QUESTIONS_NUMBER = 'questions_number';

    public const RELATION_PROFESSOR = 'professor';
    public const RELATION_ASSISTANT = 'assistant';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_PROFESSOR_ID,
        self::ATTR_ASSISTANT_ID,
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
        self::ATTR_ASSISTANT_ID => 'int',
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assistant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, self::ATTR_ASSISTANT_ID);
    }
}