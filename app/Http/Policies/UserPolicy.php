<?php declare(strict_types=1);

namespace App\Http\Policies;

use App\Enums\RolesEnum;
use App\Models\User;
use App\Parents\Policy;

/**
 * Class UserPolicy
 *
 * @package App\Http\Policies
 */
final class UserPolicy extends Policy
{
    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->role === RolesEnum::ROLE_ADMINISTRATOR;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $user2
     * @return bool
     */
    public function show(User $user, User $user2): bool
    {
        return $user->role === RolesEnum::ROLE_ADMINISTRATOR || $user->is($user2);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $user2
     * @return bool
     */
    public function edit(User $user, User $user2): bool
    {
        return $user->role === RolesEnum::ROLE_ADMINISTRATOR || $user->is($user2);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\User $user2
     * @return bool
     * @noinspection PhpUnusedParameterInspection
     */
    public function delete(User $user, User $user2): bool
    {
        return $user->role === RolesEnum::ROLE_ADMINISTRATOR;
    }
}
