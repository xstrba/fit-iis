<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Models\TestStudent;
use App\Parents\FrontEndController;
use App\Tables\TestStudentsTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TestStudentsController
 *
 * @package App\Http\Controllers\Auth
 */
final class TestStudentsController extends FrontEndController
{
    /**
     * Get data in json response and apply filters set in request
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Tables\TestStudentsTable $table
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function indexJson(
        Request $request,
        TestStudentsTable $table,
        TestsRepositoryInterface $testsRepository,
        int $id
    ): \Illuminate\Http\JsonResponse {
        $test = $testsRepository->get($id);
        $table->initializeTable($test);
        $this->authService->user();
        return $this->responseFactory->json($table->getData($request));
    }

    /**
     * @param \App\Tables\TestStudentsTable $table
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonFilters(
        TestStudentsTable $table,
        TestsRepositoryInterface $testsRepository,
        int $id
    ): JsonResponse {
        $test = $testsRepository->get($id);
        $table->initializeTable($test);
        return $this->responseFactory->json($table->getFilters());
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function request(Request $request, TestsRepositoryInterface $testsRepository, int $id)
    {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::REQUEST_STUDENT, $test);
        $test->students()->attach($this->authService->getId());

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'student requested']);
        }
        $this->notify->success($this->translator->get('messages.student.requested'), '');
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
    public function accept(
        Request $request,
        TestsRepositoryInterface $testsRepository,
        UsersRepositoryInterface $usersRepository,
        int $id,
        int $userId
    ) {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::ACCEPT_STUDENT, $test);
        $user = $usersRepository->get($userId);
        $test->students()
            ->wherePivot(TestStudent::ATTR_ACCEPTED, false)
            ->updateExistingPivot($user, [
                TestStudent::ATTR_ACCEPTED => true,
            ], true);

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'student accepted']);
        }
        $this->notify->success($this->translator->get('messages.student.accepted'), '');
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
    public function remove(
        Request $request,
        TestsRepositoryInterface $testsRepository,
        UsersRepositoryInterface $usersRepository,
        int $id,
        int $userId
    ) {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::REMOVE_STUDENT, $test);
        $user = $usersRepository->get($userId);
        $test->students()->detach($user->getKey());

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'student removed']);
        }
        $this->notify->success($this->translator->get('messages.student.removed'), '');
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
