<?php declare(strict_types=1);

namespace App\Models;

use App\Parents\Model;

/**
 * Class TestAssistant
 *
 * @property int $test_id
 * @property int $assistant_id
 * @property bool $accepted
 * @package App\Models
 */
final class TestAssistant extends Model
{
    public const ATTR_TEST_ID = 'test_id';
    public const ATTR_ASSISTANT_ID = 'assistant_id';
    public const ATTR_ACCEPTED = 'accepted';

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        self::ATTR_TEST_ID,
        self::ATTR_ASSISTANT_ID,
        self::ATTR_ACCEPTED,
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        self::ATTR_ACCEPTED => 'bool',
    ];
}
