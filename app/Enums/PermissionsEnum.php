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

    // tests
    public const REQUEST_ASSISTANT = 'requestAssistant';
    public const REMOVE_ASSISTANT = 'removeAssistant';
    public const ACCEPT_ASSISTANT = 'acceptAssistant';
    public const START_TEST = 'startTest';
    public const SOLVE_TEST = 'solveTest';
    public const FINISH_TEST = 'finishTest';
    public const REQUEST_STUDENT = 'requestStudent';
    public const REMOVE_STUDENT = 'removeStudent';
    public const ACCEPT_STUDENT = 'acceptStudent';

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
