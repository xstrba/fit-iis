<?php declare(strict_types=1);

namespace App\Tables\Filters;

/**
 * Class Filter
 *
 * @package App\Tables\Filters
 */
final class Filter
{
    public const FIELD_KEY = 'key';
    public const FIELD_LABEL = 'label';
    public const FIELD_MULTIPLE = 'multiple';
    public const FIELD_OPTIONS = 'options';

    /**
     * @var string $key
     */
    private string $key;

    /**
     * @var string $label
     */
    private string $label;

    /**
     * @var \App\Tables\Filters\FilterOption[] $options
     */
    private array $options = [];

    /**
     * @var bool $multiple
     */
    private bool $multiple = false;

    /**
     * Filter constructor.
     *
     * @param string $key
     * @param string $label
     */
    public function __construct(string $key, string $label)
    {
        $this->key = $key;
        $this->label = $label;
    }

    /**
     * Set if this filter can accept multiple values
     *
     * @param bool $bool
     */
    public function setMultiple(bool $bool = true): void
    {
        $this->multiple = $bool;
    }

    /**
     * @param \App\Tables\Filters\FilterOption[] $options
     */
    public function addOptions(array $options): void
    {
        $this->options = \array_merge($this->options, $options);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Return array data
     *
     * @return mixed[]
     */
    public function getData(): array
    {
        return [
            self::FIELD_KEY => $this->key,
            self::FIELD_LABEL => $this->label,
            self::FIELD_OPTIONS => \array_map(static function (FilterOption $option): array {
                return $option->getData();
            }, $this->options),
            self::FIELD_MULTIPLE => $this->multiple,
        ];
    }
}
