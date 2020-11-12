<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\RolesEnum;
use App\Enums\SubjectsEnum;
use App\Models\Test;
use App\Models\User;
use App\Parents\RequestFilter;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class TestRequestFilter
 *
 * @package App\Http\Requests
 */
final class TestRequestFilter extends RequestFilter
{
    public const FIELD_PROFESSOR_ID = Test::ATTR_PROFESSOR_ID;
    public const FIELD_SUBJECT = Test::ATTR_SUBJECT;
    public const FIELD_NAME = Test::ATTR_NAME;
    public const FIELD_DESCRIPTION = Test::ATTR_DESCRIPTION;
    public const FIELD_START_DATE = Test::ATTR_START_DATE;
    public const FIELD_TIME_LIMIT = Test::ATTR_TIME_LIMIT;
    public const FIELD_QUESTIONS_NUMBER = Test::ATTR_QUESTIONS_NUMBER;

    /**
     * @var \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     */
    private TestsRepositoryInterface $testsRepository;

    private UsersRepositoryInterface $usersRepository;

    /**
     * UserRequestFilter constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validatorFactory
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        Factory $validatorFactory,
        TestsRepositoryInterface $testsRepository,
        UsersRepositoryInterface $usersRepository
    ) {
        $this->testsRepository = $testsRepository;
        $this->usersRepository = $usersRepository;
        parent::__construct($validatorFactory);
    }

    /**
     * Get validate data from request
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Test|null $test
     * @return mixed[]
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated(Request $request, ?Test $test = null): array
    {
        $data = $request->all();
        $rules = $this->getRules($test);
        $validator = $this->validatorFactory->make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->getFilteredData($data, $rules);
    }

    /**
     * Get validation rules for user request
     *
     * @param \App\Models\Test|null $test
     * @return mixed[]
     */
    private function getRules(?Test $test): array
    {
        $required = $this->getSometimesRequiredRule($test);

        return [
            self::FIELD_PROFESSOR_ID => [
                $required,
                'numeric',
                function (string $attribute, int $value, callable $fail): void {
                    $this->validateProfessorId($value, $fail);
                },
            ],
            self::FIELD_SUBJECT => [$required, 'string', 'max:4', 'in:' . SubjectsEnum::instance()->getStringValues()],
            self::FIELD_NAME => [
                $required,
                'string',
                'max:255',
                function (string $attribute, string $value, callable $fail) use ($test): void {
                    $this->validateName($value, $fail, $test);
                },
            ],
            self::FIELD_DESCRIPTION => ['nullable', 'string'],
            self::FIELD_START_DATE => [$required, 'date_format:Y-m-d\TH:i'],
            self::FIELD_TIME_LIMIT => [$required, 'numeric', 'min:1'],
            self::FIELD_QUESTIONS_NUMBER => [$required, 'numeric', 'min:1'],
        ];
    }

    /**
     * Check if email is unique
     *
     * @param string $value
     * @param callable $fail
     * @param \App\Models\User|null $user
     */
    private function validateEmail(string $value, callable $fail, ?User $user): void
    {
        $actualUser = $this->usersRepository->findByEmail($value);

        if ($actualUser && !$actualUser->is($user)) {
            $fail('email_unique');
        }
    }

    /**
     * @param int $value
     * @param callable $fail
     */
    private function validateProfessorId(int $value, callable $fail): void
    {
        /** @var User|null $professor */
        $professor = $this->usersRepository->query()
            ->whereRole(RolesEnum::ROLE_PROFESSOR, '>=')
            ->whereNull(User::ATTR_DELETED_AT)
            ->findById($value);

        if (!$professor) {
            $fail('professor_not_valid');
        }
    }

    /**
     * @param string $value
     * @param callable $fail
     * @param \App\Models\Test|null $test
     */
    private function validateName(string $value, callable $fail, ?Test $test): void
    {
        $existingTest = $this->testsRepository->query()->whereName($value)->first();

        if ($existingTest && $existingTest->isNot($test)) {
            $fail('title_unique');
        }
    }
}
