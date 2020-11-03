<?php declare(strict_types=1);

namespace App\Enums;

use App\Parents\Enum;

/**
 * Class SubjectsEnum
 *
 * @package App\Enums
 */
final class SubjectsEnum extends Enum
{
    public const ITU = 'ITU';
    public const IIS = 'IIS';
    public const IMP = 'IMP';
    public const IVS = 'IVS';

    /**
     * @var string[] $values
     */
    protected array $values = [
        self::ITU,
        self::IIS,
        self::IMP,
        self::IVS,
    ];
}
