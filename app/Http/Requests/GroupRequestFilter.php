<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\Repositories\GroupsRepositoryInterface;
use App\Enums\CountriesEnum;
use App\Enums\GendersEnum;
use App\Enums\LanguagesEnum;
use App\Enums\QuestionTypesEnum;
use App\Enums\RolesEnum;
use App\Models\File;
use App\Models\Group;
use App\Models\Option;
use App\Models\Question;
use App\Models\Test;
use App\Parents\RequestFilter;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

/**
 * Class GroupRequestFilter
 *
 * @package App\Http\Requests
 */
final class GroupRequestFilter extends RequestFilter
{
    public const FIELD_NAME = Group::ATTR_NAME;
    public const FIELD_TEST_ID = Group::ATTR_TEST_ID;
    public const FIELD_QUESTIONS = Group::RELATION_QUESTIONS;
    public const FIELD_FILE_BASE64 = 'base64';

    /**
     * @var \App\Contracts\Repositories\GroupsRepositoryInterface $groupsRepository
     */
    private GroupsRepositoryInterface $groupsRepository;

    /**
     * @var \Illuminate\Http\Request|null $request
     */
    private ?Request $request = null;

    /**
     * GroupRequestFilter constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validatorFactory
     * @param \App\Contracts\Repositories\GroupsRepositoryInterface $groupsRepository
     */
    public function __construct(
        Factory $validatorFactory,
        \App\Contracts\Repositories\GroupsRepositoryInterface $groupsRepository
    ) {
        $this->groupsRepository = $groupsRepository;
        parent::__construct($validatorFactory);
    }

    /**
     * Get validate data from request
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Group|null $group
     * @return mixed[]
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated(Request $request, ?Group $group = null): array
    {
        $this->request = $request;
        $data = $request->all();
        $rules = $this->getRules($group);
        $validator = $this->validatorFactory->make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->getFilteredData($data, $rules);
    }

    /**
     * @return mixed[]|null
     */
    public function getQuestions(): ?array
    {
        if (!$this->request) {
            return null;
        }

        return $this->request->input(self::FIELD_QUESTIONS);
    }

    /**
     * Get validation rules for user request
     *
     * @param \App\Models\Group|null $group
     * @return mixed[]
     */
    private function getRules(?Group $group): array
    {
        $required = $this->getSometimesRequiredRule($group);
        return \array_merge([
            self::FIELD_NAME => [$required, 'string', 'max:255'],
            self::FIELD_TEST_ID => [$required, 'numeric', 'exists:' . Test::table() . ',id'],
        ], $this->getQuestionsRules($required));
    }

    /**
     * Rules for questions field
     *
     * @param string $required
     * @return mixed[]
     */
    private function getQuestionsRules(string $required): array
    {
        return [
            self::FIELD_QUESTIONS => [
                'sometimes',
                'array',
            ],
            self::FIELD_QUESTIONS . '.*' => [
                'array',
            ],
            self::FIELD_QUESTIONS . '.*.' . Question::ATTR_ID => ['sometimes', 'numeric', 'exists:' . Question::table() . ',id'],
            self::FIELD_QUESTIONS . '.*.' . Question::ATTR_NAME => [$required, 'string', 'max:255'],
            self::FIELD_QUESTIONS . '.*.' . Question::ATTR_TEXT => ['nullable', 'string', 'max:10000'],
            self::FIELD_QUESTIONS . '.*.' . Question::ATTR_TYPE => [$required, 'numeric', 'in:' . QuestionTypesEnum::instance()->getStringValues()],
            self::FIELD_QUESTIONS . '.*.' . Question::ATTR_MIN_POINTS => ['sometimes', 'numeric', 'max:1000'],
            self::FIELD_QUESTIONS . '.*.' . Question::ATTR_MAX_POINTS => ['sometimes', 'numeric', 'max:1000'],
            self::FIELD_QUESTIONS . '.*.' . Question::ATTR_FILES_NUMBER => ['sometimes', 'numeric', 'min:0', 'max:20'],

            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_FILES => ['sometimes', 'array'],
            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_FILES . '.*' => ['array'],
            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_FILES . '.*.' . File::ATTR_NAME => ['required', 'string', 'max:255'],
            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_FILES . '.*.' . File::ATTR_ID =>[
                'required_without:' . self::FIELD_QUESTIONS . '.*.' . Question::RELATION_FILES . '.*.' . self::FIELD_FILE_BASE64,
                'numeric',
                'exists:' . File::table() . ',id'
            ],
            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_FILES . '.*.' . self::FIELD_FILE_BASE64 => [
                'required_without:' . self::FIELD_QUESTIONS . '.*.' . Question::RELATION_FILES . '.*.' . File::ATTR_ID,
                'string',
                'max:4000000',
                function (string $attribute, string $value, callable $fail) {
                    $this->validateBase64($value, $fail);
                },
            ],

            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_OPTIONS => ['sometimes', 'array'],
            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_OPTIONS . '.*' => ['array'],
            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_OPTIONS . '.*.' . Option::ATTR_ID => [
                'sometimes',
                'numeric',
                'exists:' . Option::table() . ',id',
            ],
            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_OPTIONS . '.*.' . Option::ATTR_TEXT => [
                'required',
                'string',
                'max:10000'
            ],
            self::FIELD_QUESTIONS . '.*.' . Question::RELATION_OPTIONS . '.*.' . Option::ATTR_POINTS => [
                'sometimes',
                'numeric',
                'min:-1000',
                'max:1000',
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
            $fail('Položka musí být validní soubor');
        }
    }
}
