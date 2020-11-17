<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;
use App\Queries\GroupsQueryBuilder;

/**
 * Class Group
 *
 * @property int $test_id
 * @property string $name
 * @property \App\Models\Test $test
 * @property \Illuminate\Support\Collection|\App\Models\Question[] $questions
 * @package App\Models
 */
final class Group extends Model
{
    public const ATTR_TEST_ID = 'test_id';
    public const ATTR_NAME = 'name';

    public const RELATION_TEST = 'test';
    public const RELATION_QUESTIONS = 'questions';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_TEST_ID,
        self::ATTR_NAME,
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_TEST_ID => 'int',
        self::ATTR_NAME => 'string',
    ];

    /**
     * Get query builder for model
     *
     * @return GroupsQueryBuilder
     */
    public function newModelQuery(): GroupsQueryBuilder
    {
        return new GroupsQueryBuilder($this);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function test(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Test::class, self::ATTR_TEST_ID);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Question::class, Question::ATTR_GROUP_ID);
    }
}
