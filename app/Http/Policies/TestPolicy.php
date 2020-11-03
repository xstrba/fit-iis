<?php declare(strict_types=1);

namespace App\Http\Policies;

use App\Enums\RolesEnum;
use App\Models\Test;
use App\Models\User;
use App\Parents\Policy;

/**
 * Class TestPolicy
 *
 * @package App\Http\Policies
 */
final class TestPolicy extends Policy
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
     * @param \App\Models\Test|null $test
     * @return bool
     */
    public function show(User $user, ?Test $test = null): bool
    {
        return true;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test|null $test
     * @return bool
     */
    public function edit(User $user, ?Test $test = null): bool
    {
        if (!$test) {
            return $user->role >= RolesEnum::ROLE_PROFESSOR;
        }

        return $user->role === RolesEnum::ROLE_ADMINISTRATOR || $user->is($test->professor);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test|null $test
     * @return bool
     */
    public function delete(User $user, ?Test $test = null): bool
    {
        if (!$test) {
            return $user->role >= RolesEnum::ROLE_PROFESSOR;
        }

        return $user->role === RolesEnum::ROLE_ADMINISTRATOR || $user->is($test->professor);
    }
}
