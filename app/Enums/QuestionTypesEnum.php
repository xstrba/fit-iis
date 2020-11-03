<?php declare(strict_types=1);

namespace App\Enums;

use App\Parents\Enum;

/**
 * Class QuestionTypesEnum
 *
 * @package App\Enums
 */
final class QuestionTypesEnum extends Enum
{
    public const OPTIONS = 0;
    public const LINE = 1;
    public const TEXT = 2;

    /**
     * @var int[] $values
     */
    protected array $values = [
        self::OPTIONS,
        self::LINE,
        self::TEXT,
    ];
}
