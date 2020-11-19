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
    public const OPTIONS_CHECKBOX = 1;
    public const LINE = 2;
    public const TEXT = 3;
    public const FILES = 4;

    /**
     * @var int[] $values
     */
    protected array $values = [
        self::OPTIONS,
        self::OPTIONS_CHECKBOX,
        self::LINE,
        self::TEXT,
        self::FILES,
    ];
}
