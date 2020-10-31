<?php declare(strict_types=1);

namespace App\Enums;

use App\Parents\Enum;

/**
 * Class LanguagesEnum
 *
 * @package App\Enums
 */
final class LanguagesEnum extends Enum
{
    public const EN = 'en';
    public const CZ = 'cs';

    /**
     * @var array|string[] $values
     */
    protected array $values = [
        self::EN,
        self::CZ,
    ];
}
