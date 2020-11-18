<?php declare(strict_types=1);

namespace App\Http\Policies;

use App\Enums\RolesEnum;
use App\Models\QuestionStudent;
use App\Models\User;
use App\Parents\Policy;

/**
 * Class QuestionStudentPolicy
 *
 * @package App\Http\Policies
 */
final class QuestionStudentPolicy extends Policy
{
    /**
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\QuestionStudent|null $solution
     * @return bool
     */
    public function show(User $user, ?QuestionStudent $solution= null): bool
    {
        return false;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\QuestionStudent $solution
     * @return bool
     */
    public function edit(User $user, QuestionStudent $solution): bool
    {
        return $solution->student_id === $user->id;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\QuestionStudent|null $solution
     * @return bool
     */
    public function delete(User $user, ?QuestionStudent $solution= null): bool
    {
        return false;
    }
}
