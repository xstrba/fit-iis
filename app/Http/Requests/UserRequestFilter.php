<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\CountriesEnum;
use App\Enums\GendersEnum;
use App\Enums\LanguagesEnum;
use App\Enums\RolesEnum;
use App\Models\User;
use App\Parents\RequestFilter;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

/**
 * Class UserRequestFilter
 *
 * @package App\Http\Requests
 */
final class UserRequestFilter extends RequestFilter
{
    public const FIELD_FIRST_NAME = User::ATTR_FIRST_NAME;
    public const FIELD_LAST_NAME = User::ATTR_LAST_NAME;
    public const FIELD_EMAIL = User::ATTR_EMAIL;
    public const FIELD_NICKNAME = User::ATTR_NICKNAME;
    public const FIELD_ROLE = User::ATTR_ROLE;
    public const FIELD_PASSWORD = User::ATTR_PASSWORD;
    public const FIELD_BIRTH = User::ATTR_BIRTH;
    public const FIELD_STREET = User::ATTR_STREET;
    public const FIELD_HOUSE_NUMBER = User::ATTR_HOUSE_NUMBER;
    public const FIELD_CITY = User::ATTR_CITY;
    public const FIELD_COUNTRY = User::ATTR_COUNTRY;
    public const FIELD_GENDER = User::ATTR_GENDER;
    public const FIELD_PHONE = User::ATTR_PHONE;
    public const FIELD_LANGUAGE = User::ATTR_LANGUAGE;

    /**
     * @var \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    private UsersRepositoryInterface $usersRepository;

    /**
     * @var \Illuminate\Contracts\Hashing\Hasher $hasher
     */
    private Hasher $hasher;

    /**
     * UserRequestFilter constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validatorFactory
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     */
    public function __construct(
        Factory $validatorFactory,
        \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository,
        Hasher $hasher
    ) {
        $this->usersRepository = $usersRepository;
        $this->hasher = $hasher;
        parent::__construct($validatorFactory);
    }

    /**
     * Get validate data from request
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User|null $user
     * @return mixed[]
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validated(Request $request, ?User $user = null): array
    {
        $data = $request->all();
        $rules = $this->getRules($user);
        $validator = $this->validatorFactory->make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (Arr::exists($data, self::FIELD_PASSWORD)) {
            if (!$data[self::FIELD_PASSWORD]) {
                unset($data[self::FIELD_PASSWORD]);
            } else {
                $data[self::FIELD_PASSWORD] = $this->hasher->make($data[self::FIELD_PASSWORD]);
            }
        }

        return $this->getFilteredData($data, $rules);
    }

    /**
     * Get validation rules for user request
     *
     * @param \App\Models\User|null $user
     * @return mixed[]
     */
    private function getRules(?User $user): array
    {
        $required = $this->getSometimesRequiredRule($user);
        return [
            self::FIELD_FIRST_NAME => [
                $required,
                'string',
                'max:255',
            ],
            self::FIELD_LAST_NAME => [
                'nullable',
                'string',
                'max:255',
            ],
            self::FIELD_EMAIL => [
                $required,
                'email',
                'max:255',
                function (string $attribute, string $value, callable $fail) use ($user): void {
                    $this->validateEmail($value, $fail, $user);
                },
            ],
            self::FIELD_NICKNAME => [
                $required,
                'string',
                'max:255',
                function (string $attribute, string $value, callable $fail) use ($user): void {
                    $this->validateNickname($value, $fail, $user);
                },
            ],
            self::FIELD_ROLE => [
                $required,
                'int',
                RolesEnum::instance()->getValidationRule(),
            ],
            self::FIELD_PASSWORD => [
                'nullable',
                'string',
                'confirmed',
            ],
            self::FIELD_BIRTH => [
                $required,
                'string',
                'date_format:Y-m-d',
            ],
            self::FIELD_STREET => [$required, 'string'],
            self::FIELD_HOUSE_NUMBER => [$required, 'string'],
            self::FIELD_CITY => [$required, 'string'],
            self::FIELD_COUNTRY => [$required, 'string', CountriesEnum::instance()->getValidationRule()],
            self::FIELD_GENDER => [$required, 'string', GendersEnum::instance()->getValidationRule()],
            self::FIELD_PHONE => [$required, 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
            self::FIELD_LANGUAGE => ['sometimes', 'string', LanguagesEnum::instance()->getValidationRule()],
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
     * Check if email is unique
     *
     * @param string $value
     * @param callable $fail
     * @param \App\Models\User|null $user
     */
    private function validateNickname(string $value, callable $fail, ?User $user): void
    {
        $actualUser = $this->usersRepository->findByNickname($value);

        if ($actualUser && !$actualUser->is($user)) {
            $fail('nickname_unique');
        }
    }
}
