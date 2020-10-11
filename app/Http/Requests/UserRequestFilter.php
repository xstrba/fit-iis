<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\RolesEnum;
use App\Models\User;
use App\Parents\RequestFilter;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
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

    /**
     * @var \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    private UsersRepositoryInterface $usersRepository;

    /**
     * UserRequestFilter constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validatorFactory
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        Factory $validatorFactory,
        \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
    ) {
        $this->usersRepository = $usersRepository;
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
                'in:' . RolesEnum::instance()->getStringValues(),
            ],
            self::FIELD_PASSWORD => [
                $required,
                'string',
                'confirmed',
            ],
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
