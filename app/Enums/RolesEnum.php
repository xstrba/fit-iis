<?php declare(strict_types=1);

namespace App\Enums;

use App\Parents\Enum;

/**
 * Class RolesEnum
 *
 * @package App\Enums
 */
final class RolesEnum extends Enum
{
    /**
     * Roles have to be ordered from lowest level to highest
     * Higher level has permissions of lower level
     */
    public const ROLE_STUDENT = 0;
    public const ROLE_ASSISTANT = 1;
    public const ROLE_PROFESSOR = 2;
    public const ROLE_ADMINISTRATOR = 3;

    /**
     * @var array|int[]
     */
    protected array $values = [
        self::ROLE_STUDENT,
        self::ROLE_ASSISTANT,
        self::ROLE_PROFESSOR,
        self::ROLE_ADMINISTRATOR,
    ];
}
