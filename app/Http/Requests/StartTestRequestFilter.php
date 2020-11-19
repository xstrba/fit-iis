<?php


namespace App\Http\Requests;


use App\Enums\RolesEnum;
use App\Models\Test;
use App\Parents\RequestFilter;
use App\Services\AuthService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;

/**
 * Class StartTestRequestFilter
 *
 * @package App\Http\Requests
 */
final class StartTestRequestFilter extends RequestFilter
{
    public const FIELD_GROUP_ID = 'group_id';
    private const FIELD_TIME = 'time';

    /**
     * @var \App\Services\AuthService $authService
     */
    private AuthService $authService;

    /**
     * @var \Illuminate\Http\Request $request
     */
    private Request $request;

    /**
     * UserRequestFilter constructor.
     *
     * @param \Illuminate\Contracts\Validation\Factory $validatorFactory
     * @param \App\Services\AuthService $authService
     */
    public function __construct(
        Factory $validatorFactory,
        AuthService $authService
    ) {
        $this->authService = $authService;
        parent::__construct($validatorFactory);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Test $test
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(Request $request, Test $test): void
    {
        $this->request = $request;
        $user = $this->authService->user();
        if ($user->role < RolesEnum::ROLE_ASSISTANT) {
            if ($request->input(self::FIELD_GROUP_ID)) {
                throw ValidationException::withMessages([
                    self::FIELD_GROUP_ID => "Nemáte oprávnění si zvolit skupinu.",
                ]);
            }

            if ($test->start_date->gte(Carbon::now())) {
                throw ValidationException::withMessages([
                    self::FIELD_TIME => "Jěště nemůžete začít test.",
                ]);
            }
        }

        if ($request->input(self::FIELD_GROUP_ID)) {
            $group = $test->groups()->find($request->input(self::FIELD_GROUP_ID));
            if (!$group) {
                throw ValidationException::withMessages([
                    self::FIELD_GROUP_ID => "Skupina neexistuje.",
                ]);
            }
        }
    }

    /**
     * Get group id from request
     *
     * @return int|null
     */
    public function getGroupId(): ?int
    {
        return (int)$this->request->input(self::FIELD_GROUP_ID, 0) ?: null;
    }
}
