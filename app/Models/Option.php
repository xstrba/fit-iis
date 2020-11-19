<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;

/**
 * Class Option
 *
 * @property int $question_id
 * @property \App\Models\Question $question
 * @property string $text
 * @property int $points
 * @package App\Models
 */
final class Option extends Model
{
    public const ATTR_QUESTION_ID = 'question_id';
    public const ATTR_TEXT = 'text';
    public const ATTR_POINTS = 'points';

    public const RELATION_QUESTION = 'question';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_QUESTION_ID,
        self::ATTR_TEXT,
        self::ATTR_POINTS,
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_QUESTION_ID => 'int',
        self::ATTR_TEXT => 'string',
        self::ATTR_POINTS => 'int',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Question::class, self::ATTR_QUESTION_ID);
    }
}
