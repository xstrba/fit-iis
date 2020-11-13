<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Http\Requests\TestRequestFilter;
use App\Models\Test;
use App\Models\TestAssistant;
use App\Models\User;
use App\Parents\FrontEndController;
use App\Queries\UsersQueryBuilder;
use App\Services\SidebarService;
use App\Tables\TestsTable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
            ->where(User::ATTR_ROLE, '>=', RolesEnum::ROLE_PROFESSOR)
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
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(TestsRepositoryInterface $testsRepository, int $id)
    {
        /** @var Test $test */
        $test = $testsRepository
            ->query()
            ->with([
                Test::RELATION_ASSISTANTS => static function (BelongsToMany $query): void {
                    $query->where(TestAssistant::table() . '.' . TestAssistant::ATTR_ACCEPTED, true);
                },
                Test::RELATION_PROFESSOR,
            ])
            ->getById($id);
        $this->authorize(PermissionsEnum::SHOW, $test);
        $this->setTitle('pages.tests.detail', ['test' => $test->name]);
        return $this->view('app.tests.detail', compact('test'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(UsersRepositoryInterface $usersRepository, TestsRepositoryInterface $testsRepository, int $id)
    {
        /** @var Test $test */
        $test = $testsRepository->query()
            ->with(Test::RELATION_PROFESSOR)
            ->with(Test::RELATION_ASSISTANTS)
            ->getById($id);
        $this->authorize(PermissionsEnum::EDIT, $test);

        $professors = $usersRepository->query()
            ->where(User::ATTR_ROLE, '>=', RolesEnum::ROLE_PROFESSOR)
            ->get();
        $this->setTitle('pages.tests.edit', ['test' => $test->name]);
        return $this->view('app.tests.form', compact('test', 'professors'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Http\Requests\TestRequestFilter $testRequestFilter
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(
        Request $request,
        TestRequestFilter $testRequestFilter,
        TestsRepositoryInterface $testsRepository,
        int $id
    ): \Illuminate\Http\RedirectResponse {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::EDIT, $test);
        $testsRepository->update($testRequestFilter->validated($request, $test), $test);
        $this->notify->success($this->translator->get('messages.tests.updated'));
        return $this->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(Request $request, TestsRepositoryInterface $testsRepository, int $id)
    {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::DELETE, $test);
        $testsRepository->delete($test);

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'deleted']);
        }
        $this->notify->success($this->translator->get('messages.tests.deleted'));
        return $this->back();
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Contracts\Repositories\TestsRepositoryInterface $testsRepository
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Request $request, TestsRepositoryInterface $testsRepository, int $id)
    {
        $test = $testsRepository->get($id);
        $this->authorize(PermissionsEnum::EDIT, $test);
        $testsRepository->restore($test);

        if ($request->wantsJson()) {
            return $this->responseFactory->json(['message' => 'restored']);
        }
        $this->notify->success($this->translator->get('messages.tests.restored'));
        return $this->back();
    }

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
        $this->notify->success($this->translator->get('messages.assistant.requested'));
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
        $this->notify->success($this->translator->get('messages.assistant.accepted'));
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
            return $this->responseFactory->json(['message' => 'assistant accepted']);
        }
        $this->notify->success($this->translator->get('messages.assistant.accepted'));
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