<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Http\Requests\StartTestRequestFilter;
use App\Http\Requests\TestSolutionRequestFilter;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Question;
use App\Models\QuestionStudent;
use App\Parents\FrontEndController;
use Illuminate\Auth\Access\AuthorizationException;
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
        $user = $this->authService->user();
        $this->authorize(PermissionsEnum::START_TEST, $test);
        $filter->validate($request, $test);
        $groupId = $filter->getGroupId();
        $groupIds = $test->groups->pluck(Group::ATTR_ID)->toArray();
        if (!$groupId) {
            /** @var Group|null $group */
            $group = $user->testSolutions->whereIn(GroupStudent::ATTR_GROUP_ID, $groupIds)->first();
            $groupId = optional($group)->id ?? $groupIds[\array_rand($groupIds, 1)];
        }

        $user->testSolutions()->whereIn(GroupStudent::ATTR_GROUP_ID, $groupIds)->delete();

        /** @var GroupStudent|null $solution */
        $solution = $user->testSolutions()
            ->where(GroupStudent::ATTR_GROUP_ID, $groupId)
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

        // delete old question solutions
        $user->questionSolutions()
            ->whereIn(QuestionStudent::ATTR_QUESTION_ID,
                $solution->group->questions->pluck(Question::ATTR_ID)->toArray())
            ->forceDelete();

        // create new question solutions
        foreach ($solution->group->questions->random($test->questions_number) as $question) {
            $user->questionSolutions()->create([
                QuestionStudent::ATTR_QUESTION_ID => $question->id,
            ]);
        }

        return $this->responseFactory->redirectToRoute('tests.solution', ['id' => $test->id, 'group_id' => $groupId]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Http\Requests\TestSolutionRequestFilter $filter
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function solution(
        Request $request,
        TestSolutionRequestFilter $filter,
        TestsRepositoryInterface $testsRepository,
        int $id
    ): \Illuminate\Contracts\View\View {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::SOLVE_TEST, $test);
        $user = $this->authService->user();
        $filter->validate($request, $test);
        if ($filter->getGroupId()) {
            $groupIds = [$filter->getGroupId()];
        } else {
            $groupIds = $test->groups->pluck(Group::ATTR_ID)->toArray();
        }

        /** @var GroupStudent $groupSolution */
        $groupSolution = $user->testSolutions()
            ->whereIn(GroupStudent::ATTR_GROUP_ID, $groupIds)
            ->first();

        if (!$groupSolution) {
            throw new ModelNotFoundException('Test not found');
        }
        $groupSolution->load([
            GroupStudent::RELATION_GROUP,
        ]);

        $questionSolutions = $user->questionSolutions()
            ->whereIn(QuestionStudent::ATTR_QUESTION_ID, $groupSolution->group->questions->pluck(Question::ATTR_ID)->toArray())
            ->oldest()
            ->get();

        $this->setTitle('pages.tests.solution', ['group' => $groupSolution->group->name, 'test' => $groupSolution->group->test->name]);
        return $this->view('app.tests.solutions.index', compact('groupSolution', 'questionSolutions'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function finish(Request $request, TestsRepositoryInterface $testsRepository, int $id)
    {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::FINISH_TEST, $test);

        /** @var GroupStudent|null $solution */
        $solution = $this->authService->user()->testSolutions()->whereIn(
            GroupStudent::ATTR_GROUP_ID,
            $test->groups->pluck(Group::ATTR_ID)->toArray()
        )->first();

        if (!$solution) {
            throw new AuthorizationException();
        }

        $solution->compactFill([
            GroupStudent::ATTR_FINISHED => true,
        ]);
        $solution->save();

        $this->notify->success('Test ukončen', '');

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['redirect' => $this->urlGenerator->route('tests.show', $test->id)]);
        }
        return $this->responseFactory->redirectToRoute('tests.show', $test->id);
    }

    /**
     * @inheritDoc
     */
    protected function initMiddleware(): void
    {
        $this->middleware('auth');
    }
}
