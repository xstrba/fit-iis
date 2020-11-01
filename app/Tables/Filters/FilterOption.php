<?php declare(strict_types=1);

namespace App\Tables\Filters;

/**
 * Class FilterOption
 *
 * @package App\Tables\Filters
 */
final class FilterOption
{
    public const FIELD_VALUE = 'value';
    public const FIELD_LABEL = 'label';

    /**
     * @var mixed $value
     */
    private $value;

    /**
     * @var string $label
     */
    private string $label;

    /**
     * FilterOption constructor.
     *
     * @param mixed $value
     * @param string $label
     */
    public function __construct($value, string $label)
    {
        $this->value = $value;
        $this->label = $label;
    }

    /**
     * Initialize new option
     *
     * @param mixed $value
     * @param string $label
     * @return $this
     */
    public static function init($value, string $label): self
    {
        return new self($value, $label);
    }

    /**
     * Get option data
     *
     * @return mixed[]
     */
    public function getData(): array
    {
        return [
            self::FIELD_VALUE => $this->value,
            self::FIELD_LABEL => $this->label,
        ];
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }
}
