<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Enums\PermissionsEnum;
use App\Http\Requests\UserRequestFilter;
use App\Models\User;
use App\Parents\FrontEndController;
use App\Queries\UsersQueryBuilder;
use App\Tables\UsersTable;
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
        $this->authorize(PermissionsEnum::CREATE, User::class);
        $this->setTitle('pages.users.index');
        return $this->view('app.users.index', compact('table'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Tables\UsersTable $table
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function indexJson(Request $request, UsersTable $table): \Illuminate\Http\JsonResponse
    {
        $this->authorize(PermissionsEnum::CREATE, User::class);
        return $this->responseFactory->json($table->getData($request));
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
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

        $this->notify->success('Profile info updated');
        return $this->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @inheritDoc
     */
    protected function initMiddleware(): void
    {
        $this->middleware('auth');
    }
}
