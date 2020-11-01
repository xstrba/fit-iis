<?php declare(strict_types=1);

namespace App\Parents;

/**
 * Class Enum
 *
 * @package App\Parents
 */
class Enum
{
    /**
     * @var mixed[] $values
     */
    protected array $values = [];

    /**
     * Enum constructor. Disable creating instances with new
     *
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * Get valid values of enum
     *
     * @return mixed[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return mixed[]
     */
    public function getList(): array
    {
        $values = $this->getValues();
        return \array_combine($values, $values);
    }

    /**
     * Get valid values of model in string for validation
     *
     * @return string
     */
    public function getStringValues(): string
    {
        return \implode(',', $this->getValues());
    }

    /**
     * @return string
     */
    public function getValidationRule(): string
    {
        return 'in:' . $this->getStringValues();
    }

    /**
     * Get enum instance
     *
     * @return \App\Parents\Enum
     */
    public static function instance(): Enum
    {
        return new static();
    }
}
