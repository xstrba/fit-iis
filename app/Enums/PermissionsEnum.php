<?php declare(strict_types=1);

namespace App\Enums;

use App\Parents\Enum;

/**
 * Class PermissionsEnum
 *
 * @package App\Enums
 */
final class PermissionsEnum extends Enum
{
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const SHOW = 'show';
    public const DELETE = 'delete';

    /**
     * @var array|int[]
     */
    protected array $values = [
        self::CREATE,
        self::EDIT,
        self::SHOW,
        self::DELETE,
    ];
}
