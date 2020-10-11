<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Models\User;
use App\Parents\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

/**
 * Class LoginController
 *
 * @package App\Http\Controllers\Auth
 */
final class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string $redirectTo
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * @var \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    private UsersRepositoryInterface $usersRepository;

    /**
     * Create a new controller instance.
     *
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    public function __construct(UsersRepositoryInterface $usersRepository)
    {
        $this->middleware('guest')->except('logout');
        $this->usersRepository = $usersRepository;
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * @noinspection PhpDocRedundantThrowsInspection
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string|max:255',
            User::ATTR_PASSWORD => 'required|string|max:255',
            User::ATTR_REMEMBER_TOKEN => 'sometimes|bool'
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function attemptLogin(Request $request)
    {
        /** @var User|null $user */
        $user = $this->usersRepository->query()->whereEmailOrNickname(
            $request->input($this->username(), '')
        )->getFirst();

        $credentials = $this->credentials($request);
        if ($user) {
            $credentials[$this->username()] = $user->getAttributeValue($this->username());
        }

        return $this->guard()->attempt(
            $credentials, $request->filled('remember')
        );
    }
}
