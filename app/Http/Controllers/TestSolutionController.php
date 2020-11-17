<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Http\Requests\StartTestRequestFilter;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Parents\FrontEndController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Class TestSolutionController
 *
 * @package App\Http\Controllers
 */
final class TestSolutionController extends FrontEndController
{
    /**
     * Get random gorup for user or the one specified in request.
     * Then create item and redirect to test solution.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Http\Requests\StartTestRequestFilter $filter
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function start(
        Request $request,
        StartTestRequestFilter $filter,
        TestsRepositoryInterface $testsRepository,
        int $id
    ): \Illuminate\Http\RedirectResponse {
        $test = $testsRepository->get($id);
        $user = $test-$this->authService->user();
        $this->authorize(PermissionsEnum::START_TEST, $test);
        $filter->validate($request, $test);
        $groupId = $filter->getGroupId();
        if (!$groupId) {
            $groupId = $test->groups->random(1)->first()->id;
        }

        /** @var GroupStudent|null $solution */
        $solution = $user->testSolutions()
            ->whereIn(GroupStudent::ATTR_GROUP_ID, $test->groups->pluck(Group::ATTR_ID)->toArray())
            ->first();

        if (!$solution) {
            $solution = $user->testSolutions()->create([
                GroupStudent::ATTR_GROUP_ID => $groupId,
            ]);
        } else {
            $solution->compactFill([
                GroupStudent::ATTR_GROUP_ID => $groupId,
                GroupStudent::ATTR_FINISHED => false,
            ]);
            $solution->save();
        }

        foreach ($solution->group->questions->random($test->questions_number) as $question) {
        }

        return $this->responseFactory->redirectToRoute('tests.solution', $test->id);
    }

    /**
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function solution(TestsRepositoryInterface $testsRepository, int $id)
    {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::SOLVE_TEST, $test);
        $user = $this->authService->user();
        $groupIds = $test->groups->pluck(Group::ATTR_ID)->toArray();
        $solution = $user->testSolutions()
            ->whereIn(GroupStudent::ATTR_GROUP_ID, $groupIds)
            ->first();

        if (!$solution) {
            throw new ModelNotFoundException('Test not found');
        }
        $solution->load([
            GroupStudent::RELATION_GROUP,
        ]);

        return $this->view('app.tests.solutions.index', compact('solution'));
    }

    /**
     * @inheritDoc
     */
    protected function initMiddleware(): void
    {
        $this->middleware('auth');
    }
}
