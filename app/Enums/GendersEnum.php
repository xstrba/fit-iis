<?php declare(strict_types=1);

namespace App\Enums;

use App\Parents\Enum;

/**
 * Class GendersEnum
 *
 * @package App\Enums
 */
final class GendersEnum extends Enum
{
    public const MALE = 'male';
    public const FEMALE = 'female';

    /**
     * @var array|string[] $values
     */
    protected array $values = [
        self::MALE,
        self::FEMALE,
    ];
}
