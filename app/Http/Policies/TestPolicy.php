<?php declare(strict_types=1);

namespace App\Http\Policies;

use App\Enums\RolesEnum;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Test;
use App\Models\TestAssistant;
use App\Models\User;
use App\Parents\Policy;
use Illuminate\Support\Carbon;

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

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test $test
     * @return bool
     */
    public function requestAssistant(User $user, Test $test): bool
    {
        return $user->role >= RolesEnum::ROLE_ASSISTANT &&
            $test->professor->isNot($user) &&
            !$test->assistants->contains($user->getKey());
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test $test
     * @return bool
     */
    public function removeAssistant(User $user, Test $test): bool
    {
        return $user->role === RolesEnum::ROLE_ADMINISTRATOR || $test->professor->is($user);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test $test
     * @return bool
     */
    public function acceptAssistant(User $user, Test $test): bool
    {
        return $user->role === RolesEnum::ROLE_ADMINISTRATOR || $test->professor->is($user);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test $test
     * @return bool
     */
    public function startTest(User $user, Test $test): bool
    {
        if (!$test->isValid()) {
            return false;
        }

        if (
            $user->role === RolesEnum::ROLE_ADMINISTRATOR ||
            $test->professor_id === $user->id ||
            $test->assistants()->wherePivot(TestAssistant::ATTR_ACCEPTED, true)->get()->contains($user->getKey())
        ) {
            return !$user->testSolutions->whereIn(GroupStudent::ATTR_GROUP_ID,
                $test->groups->pluck(Group::ATTR_ID)->toArray())
                ->where(GroupStudent::ATTR_FINISHED, false)
                ->first();
        }

        return $test->start_date->lte(Carbon::now()) &&
            !$user->testSolutions->whereIn(GroupStudent::ATTR_GROUP_ID, $test->groups->pluck(Group::ATTR_ID)->toArray())
                ->first();
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test $test
     * @return bool
     */
    public function solveTest(User $user, Test $test): bool
    {
        if (!$test->isValid()) {
            return false;
        }

        if ($user->role < RolesEnum::ROLE_ASSISTANT && $test->start_date->addMinutes($test->time_limit)->gte(Carbon::now())) {
            return false;
        }

        return (bool)$user->testSolutions()
            ->whereIn(GroupStudent::ATTR_GROUP_ID, $test->groups->pluck(Group::ATTR_ID)->toArray())
            ->where(GroupStudent::ATTR_FINISHED, false)
            ->first();
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test $test
     * @return bool
     */
    public function finishTest(User $user, Test $test): bool
    {
        if (!$test->isValid()) {
            return false;
        }

        if ($user->role === RolesEnum::ROLE_ADMINISTRATOR) {
            return true;
        }

        if ($user->role === RolesEnum::ROLE_PROFESSOR && $user->id === $test->professor_id) {
            return true;
        }

        return $test->assistants()->wherePivot(TestAssistant::ATTR_ACCEPTED, true)->get()->contains($user->id) ||
            $user->testSolutions()->whereIn(
                GroupStudent::ATTR_GROUP_ID,
                $test->groups->pluck(Group::ATTR_ID)->toArray()
            )->where(GroupStudent::ATTR_FINISHED, false)->first();
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test $test
     * @return bool
     */
    public function requestStudent(User $user, Test $test): bool
    {
        if (!$test->isValid()) {
            return false;
        }

        $isNotStudent = !$test->students->contains($user->id);
        if ($user->role >= RolesEnum::ROLE_ASSISTANT) {
            return $isNotStudent;
        }

        return $isNotStudent && $test->start_date->gt(Carbon::now());
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test $test
     * @return bool
     */
    public function acceptStudent(User $user, Test $test): bool
    {
        if (!$test->isValid()) {
            return false;
        }

        return $test->assistants->contains($user->id) || $test->professor_id === $user->id || $user->role >= RolesEnum::ROLE_PROFESSOR;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Test $test
     * @return bool
     */
    public function removeStudent(User $user, Test $test): bool
    {
        return $test->assistants->contains($user->id) || $test->professor_id === $user->id || $user->role >= RolesEnum::ROLE_PROFESSOR;
    }
}
