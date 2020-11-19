<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\QuestionStudent;
use App\Parents\RequestFilter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class EvaluationRequestFilter
 *
 * @package App\Http\Requests
 */
final class EvaluationRequestFilter extends RequestFilter
{
    public const FIELD_SOLUTIONS = 'solutions';

    /**
     * @var \Illuminate\Http\Request $request
     */
    private Request $request;

    /**
     * @return array|mixed[]
     */
    public function getSolutions(): array
    {
        return $this->request->input(self::FIELD_SOLUTIONS, []);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return mixed[]
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated(Request $request): array
    {
        $this->request = $request;
        $data = $request->all();
        $rules = $this->getRules();
        $validator = $this->validatorFactory->make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $this->getFilteredData($data, $rules);
    }

    /**
     * @return mixed[]
     */
    private function getRules(): array
    {
        return [
            self::FIELD_SOLUTIONS => ['required', 'array'],
            self::FIELD_SOLUTIONS . '.*' => ['array'],
            self::FIELD_SOLUTIONS . '.*.' . QuestionStudent::ATTR_ID => [
                'numeric',
                'exists:' . QuestionStudent::table() . ',id',
            ],
            self::FIELD_SOLUTIONS . '.*.' . QuestionStudent::ATTR_POINTS => [
                'numeric',
                'min:-1000',
                'max:1000',
            ],
            self::FIELD_SOLUTIONS . '.*.' . QuestionStudent::ATTR_NOTES => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }
}
