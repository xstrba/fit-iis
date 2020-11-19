<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Models\TestAssistant;
use App\Models\TestStudent;
use App\Parents\FrontEndController;
use App\Tables\TestStudentsTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TestAssistantsController
 *
 * @package App\Http\Controllers\Auth
 */
final class TestAssistantsController extends FrontEndController
{

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function requestAssistant(Request $request, TestsRepositoryInterface $testsRepository, int $id)
    {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::REQUEST_ASSISTANT, $test);
        $test->assistants()->attach($this->authService->getId());

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'assistant requested']);
        }
        $this->notify->success($this->translator->get('messages.assistant.requested'), '');
        return $this->back();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param int $id
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function acceptAssistant(
        Request $request,
        TestsRepositoryInterface $testsRepository,
        UsersRepositoryInterface $usersRepository,
        int $id,
        int $userId
    ) {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::ACCEPT_ASSISTANT, $test);
        $user = $usersRepository->get($userId);
        $test->assistants()
            ->wherePivot(TestAssistant::ATTR_ACCEPTED, false)
            ->updateExistingPivot($user, [
                TestAssistant::ATTR_ACCEPTED => true,
            ], true);

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'assistant accepted']);
        }
        $this->notify->success($this->translator->get('messages.assistant.accepted'), '');
        return $this->back();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param int $id
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function removeAssistant(
        Request $request,
        TestsRepositoryInterface $testsRepository,
        UsersRepositoryInterface $usersRepository,
        int $id,
        int $userId
    ) {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::REMOVE_ASSISTANT, $test);
        $user = $usersRepository->get($userId);
        $test->assistants()->detach($user->getKey());

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'assistant removed']);
        }
        $this->notify->success($this->translator->get('messages.assistant.removed'), '');
        return $this->back();
    }

    /**
     * @inheritDoc
     */
    protected function initMiddleware(): void
    {
        $this->middleware('auth');
    }
}
