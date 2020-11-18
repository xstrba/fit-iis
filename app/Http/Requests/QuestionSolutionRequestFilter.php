<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\File;
use App\Models\Option;
use App\Models\QuestionStudent;
use App\Parents\RequestFilter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class QuestionSolutionRequestFilter
 *
 * @package App\Http\Requests
 */
final class QuestionSolutionRequestFilter extends RequestFilter
{
    public const FIELD_TEXT = QuestionStudent::ATTR_TEXT;
    public const FIELD_NOTES = QuestionStudent::ATTR_NOTES;
    public const FIELD_POINTS = QuestionStudent::ATTR_POINTS;
    public const FIELD_OPTIONS = QuestionStudent::RELATION_OPTIONS;
    public const FIELD_FILES = QuestionStudent::RELATION_FILES;
    public const FIELD_FILE_BASE64 = 'base64';

    /**
     * @var \Illuminate\Http\Request $request
     */
    private Request $request;

    /**
     * @return mixed[]|null
     */
    public function getOptions(): ?array
    {
        return $this->request->input(self::FIELD_OPTIONS);
    }

    /**
     * @return mixed[]|null
     */
    public function getFiles(): ?array
    {
        return $this->request->input(self::FIELD_FILES);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\QuestionStudent $solution
     * @return mixed[]
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated(Request $request, QuestionStudent $solution): array
    {
        $this->request = $request;
        $data = $request->all();
        $rules = $this->getRules($solution);
        $validator = $this->validatorFactory->make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->getFilteredData($data, $rules);
    }

    /**
     * @param \App\Models\QuestionStudent $solution
     * @return mixed[]
     */
    private function getRules(QuestionStudent $solution): array
    {
        $required = 'sometimes';

        return \array_merge([
            self::FIELD_TEXT => ['nullable', 'string', 'max:10000'],
            self::FIELD_NOTES => ['nullable', 'string', 'max:10000'],
            self::FIELD_POINTS => [$required, 'numeric', 'min:-1000', 'max:1000'],
            self::FIELD_OPTIONS => [$required, 'array'],
            self::FIELD_OPTIONS . '.*' => [
                'numeric',
                'exists:' . Option::table() . ',id'
            ],
            self::FIELD_FILES => [$required, 'array'],
        ], $this->getFilesRules($required));
    }

    /**
     * @param string $required
     * @return mixed[]
     */
    private function getFilesRules(string $required): array
    {
        return [
            QuestionStudent::RELATION_FILES . '.*' => [$required, 'array'],
            QuestionStudent::RELATION_FILES . '.*.' . File::ATTR_NAME => ['required', 'string', 'max:255'],
            QuestionStudent::RELATION_FILES . '.*.' . File::ATTR_ID =>[
                'required_without:' . QuestionStudent::RELATION_FILES . '.*.' . self::FIELD_FILE_BASE64,
                'numeric',
                'exists:' . File::table() . ',id'
            ],
            QuestionStudent::RELATION_FILES . '.*.' . self::FIELD_FILE_BASE64 => [
                'required_without:' . QuestionStudent::RELATION_FILES . '.*.' . File::ATTR_ID,
                'string',
                'max:4000000',
                function (string $attribute, string $value, callable $fail) {
                    $this->validateBase64($value, $fail);
                },
            ],
        ];
    }

    /**
     * @param string $value
     * @param callable $fail
     */
    private function validateBase64(string $value, callable $fail): void
    {
        $data = \explode(',', $value)[1] ?? '';
        if (\base64_encode(\base64_decode($data, true)) !== $data) {
            $fail('invalid_base64');
        }
    }
}
