<?php declare(strict_types=1);

namespace App\Http\Policies;

use App\Enums\RolesEnum;
use App\Models\Group;
use App\Models\User;
use App\Parents\Policy;

/**
 * Class GroupPolicy
 *
 * @package App\Http\Policies
 */
final class GroupPolicy extends Policy
{
    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->role >= RolesEnum::ROLE_PROFESSOR;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Group|null $group
     * @return bool
     */
    public function show(User $user, ?Group $group= null): bool
    {
        return true;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Group|null $group
     * @return bool
     */
    public function edit(User $user, ?Group $group= null): bool
    {
        if (!$group) {
            return $user->role >= RolesEnum::ROLE_PROFESSOR;
        }

        return $user->role === RolesEnum::ROLE_ADMINISTRATOR || $group->test->professor->is($user);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Group|null $group
     * @return bool
     */
    public function delete(User $user, ?Group $group= null): bool
    {
        if (!$group) {
            return $user->role >= RolesEnum::ROLE_PROFESSOR;
        }

        return $user->role === RolesEnum::ROLE_ADMINISTRATOR || $group->test->professor->is($user);
    }
}
