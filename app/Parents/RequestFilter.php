<?php declare(strict_types=1);

namespace App\Parents;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;

/**
 * Class RequestFilter
 *
 * @package App\Parents
 */
abstract class RequestFilter
{
    /**
     * @var \Illuminate\Contracts\Validation\Factory $validator
     */
    protected Factory $validatorFactory;

    /**
     * RequestFilter constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validatorFactory
     */
    public function __construct(Factory $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;
    }

    /**
     * Rule for fields that are required only if model does not exist
     *
     * @param \App\Parents\Model|null $model
     * @return string
     */
    protected function getSometimesRequiredRule(?Model $model = null): string
    {
        return $model && $model->exists ? 'sometimes' : 'required';
    }

    /**
     * Get only fields from data that have rule
     *
     * @param mixed[] $data
     * @param mixed[] $rules
     * @return mixed[]
     */
    protected function getFilteredData(array $data, array $rules): array
    {
        return \array_filter($data, static function ($key) use ($rules): bool {
            // check if key exists in rules
            return Arr::exists($rules, $key);
        }, ARRAY_FILTER_USE_KEY);
    }
}
