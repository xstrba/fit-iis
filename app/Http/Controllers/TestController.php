<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Http\Requests\TestRequestFilter;
use App\Http\Requests\UserRequestFilter;
use App\Models\Test;
use App\Models\User;
use App\Parents\FrontEndController;
use App\Services\SidebarService;
use App\Tables\TestsTable;
use App\Tables\UsersTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Class TestController
 *
 * @package App\Http\Controllers
 */
final class TestController extends FrontEndController
{
    /**
     * @var string|null $activeItem
     */
    protected ?string $activeItem = SidebarService::ITEM_TESTS;

    /**
     * Display a listing of the resource.
     *
     * @param \App\Tables\TestsTable $table
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(TestsTable $table): \Illuminate\Contracts\View\View
    {
        $this->setTitle('pages.tests.index');
        return $this->view('app.tests.index', compact('table'));
    }

    /**
     * Get data in json response and apply filters set in request
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Tables\TestsTable $table
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function indexJson(Request $request, TestsTable $table): \Illuminate\Http\JsonResponse
    {
        $this->authService->user();
        return $this->responseFactory->json($table->getData($request));
    }

    /**
     * @param \App\Tables\TestsTable $table
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonFilters(TestsTable $table): JsonResponse
    {
        return $this->responseFactory->json($table->getFilters());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @return \Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(UsersRepositoryInterface $usersRepository): \Illuminate\Contracts\View\View
    {
        $this->authorize(PermissionsEnum::CREATE, Test::class);
        $professors = $usersRepository->query()
            ->where(User::ATTR_ROLE ,'>=', RolesEnum::ROLE_PROFESSOR)
            ->get();

        $this->setTitle('pages.tests.create');
        $test = new Test([
            Test::ATTR_TIME_LIMIT => 10,
            Test::ATTR_START_DATE => Carbon::now(),
            Test::ATTR_QUESTIONS_NUMBER => 1,
            Test::ATTR_PROFESSOR_ID => $this->authService->getId(),
        ]);
        return $this->view('app.tests.form', compact('test', 'professors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Http\Requests\TestRequestFilter $filter
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(
        Request $request,
        TestRequestFilter $filter,
        TestsRepositoryInterface $testsRepository
    ): \Illuminate\Http\RedirectResponse {
        $this->authorize(PermissionsEnum::CREATE, Test::class);
        $testsRepository->create($filter->validated($request));
        $this->notify->success($this->translator->get('messages.tests.created'));
        return $this->responseFactory->redirectToRoute('tests.index');
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
        $user = $usersRepository->get($id);
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
