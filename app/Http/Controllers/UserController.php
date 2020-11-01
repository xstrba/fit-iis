<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Http\Requests\UserRequestFilter;
use App\Models\User;
use App\Parents\FrontEndController;
use App\Tables\UsersTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class UserController
 *
 * @package App\Http\Controllers
 */
final class UserController extends FrontEndController
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Tables\UsersTable $table
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(UsersTable $table): \Illuminate\Contracts\View\View
    {
        $this->setTitle('pages.users.index');
        return $this->view('app.users.index', compact('table'));
    }

    /**
     * Get data in json response and apply filters set in request
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Tables\UsersTable $table
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function indexJson(Request $request, UsersTable $table): \Illuminate\Http\JsonResponse
    {
        $this->authService->user();
        return $this->responseFactory->json($table->getData($request));
    }

    /**
     * @param \App\Tables\UsersTable $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonFilters(UsersTable $table): JsonResponse
    {
        return $this->responseFactory->json($table->getFilters());
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function profile(): \Illuminate\Contracts\View\View
    {
        $this->setTitle('pages.profile');
        return $this->view('app.users.profile');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(): \Illuminate\Contracts\View\View
    {
        $this->authorize(PermissionsEnum::CREATE, User::class);
        $this->setTitle('pages.users.create');
        $user = new User();
        return $this->view('app.users.form', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Http\Requests\UserRequestFilter $filter
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(
        Request $request,
        UserRequestFilter $filter,
        UsersRepositoryInterface $usersRepository
    ): \Illuminate\Http\RedirectResponse {
        $this->authorize(PermissionsEnum::CREATE, User::class);
        $usersRepository->create($filter->validated($request));
        $this->notify->success($this->translator->get('messages.users.created'));
        return $this->responseFactory->redirectToRoute('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(UsersRepositoryInterface $usersRepository, int $id)
    {
        $user = $usersRepository->get($id);
        $this->authorize(PermissionsEnum::SHOW, $user);
        $this->setTitle('pages.users.detail', ['user' => $user->nickname]);

        return $this->view('app.users.detail', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(UsersRepositoryInterface $usersRepository, int $id)
    {
        $user = $usersRepository->get($id);
        $auth = $this->authService->user();
        if ($user->is($auth)) {
            return $this->responseFactory->redirectToRoute('profile');
        }
        $this->authorize(PermissionsEnum::EDIT, $user);
        $this->setTitle('pages.users.edit', ['user' => $user->nickname]);

        return $this->view('app.users.form', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Http\Requests\UserRequestFilter $userRequestFilter
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(
        Request $request,
        UserRequestFilter $userRequestFilter,
        UsersRepositoryInterface $usersRepository,
        int $id
    ): \Illuminate\Http\RedirectResponse {
        /** @var \App\Models\User $user */
        $user = $usersRepository->query()->getById($id);
        $this->authorize(PermissionsEnum::EDIT, $user);
        $usersRepository->update($userRequestFilter->validated($request, $user), $user);

        $this->notify->success($this->translator->get('messages.users.updated'));
        return $this->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(Request $request, UsersRepositoryInterface $usersRepository, int $id)
    {
        $user = $usersRepository->get($id);
        $this->authorize(PermissionsEnum::DELETE, $user);
        $usersRepository->delete($user);

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'deleted']);
        }
        $this->notify->success($this->translator->get('messages.users.deleted'));
        return $this->back();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, UsersRepositoryInterface $usersRepository, int $id)
    {
        $user = $usersRepository->get($id);
        $this->authorize(PermissionsEnum::EDIT, $user);
        $usersRepository->restore($user);

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'restored']);
        }
        $this->notify->success($this->translator->get('messages.users.restored'));
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
