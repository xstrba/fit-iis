<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;

/**
 * Class QuestionStudentOption
 *
 * @property int $question_student_id
 * @property int $option_id
 * @package App\Models
 */
final class QuestionStudentOption extends Model
{
    public const ATTR_QUESTION_STUDENT_ID = 'question_student_id';
    public const ATTR_OPTION_ID = 'option_id';


    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_QUESTION_STUDENT_ID,
        self::ATTR_OPTION_ID,
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_QUESTION_STUDENT_ID => 'int',
        self::ATTR_OPTION_ID => 'int',
    ];
}
