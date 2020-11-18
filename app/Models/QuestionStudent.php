<?php declare(strict_types=1);

namespace App\Models;

use App\Contracts\Traits\HasFileTraitInterface;
use App\Models\Traits\HasFilesTrait;
use App\Parents\Model;
use App\Queries\QuestionStudentQueryBuilder;

/**
 * Class QuestionStudent
 *
 * @property int $question_id
 * @property int $student_id
 * @property \App\Models\Question $question
 * @property \App\Models\User $student
 * @property \App\Models\User $user
 * @property \Illuminate\Support\Collection|\App\Models\Option $options
 * @property \Illuminate\Support\Collection|\App\Models\File[] $files
 * @package App\Models
 */
final class QuestionStudent extends Model implements HasFileTraitInterface
{
    use HasFilesTrait;

    public const ATTR_QUESTION_ID = 'question_id';
    public const ATTR_STUDENT_ID = 'student_id';
    public const ATTR_TEXT = 'text';
    public const ATTR_NOTES = 'notes';
    public const ATTR_POINTS = 'points';

    public const RELATION_QUESTION = 'question';
    public const RELATION_STUDENT = 'student';
    public const RELATION_OPTIONS = 'options';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_QUESTION_ID,
        self::ATTR_STUDENT_ID,
        self::ATTR_TEXT,
        self::ATTR_NOTES,
        self::ATTR_POINTS,
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_QUESTION_ID => 'int',
        self::ATTR_STUDENT_ID => 'int',
    ];

    /**
     * Always loaded relations
     *
     * @var string[] $with
     */
    protected $with = [
        self::RELATION_OPTIONS,
        self::RELATION_FILES,
    ];

    /**
     * Get query builder for model
     *
     * @return \App\Queries\QuestionStudentQueryBuilder
     */
    public function newModelQuery(): QuestionStudentQueryBuilder
    {
        return new QuestionStudentQueryBuilder($this);
    }

    /**
     * Test instance belongs to one group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Question::class, self::ATTR_QUESTION_ID);
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function options(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Option::class,
            QuestionStudentOption::class,
            QuestionStudentOption::ATTR_QUESTION_STUDENT_ID,
            QuestionStudentOption::ATTR_OPTION_ID
        );
    }
}
