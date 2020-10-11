<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Parents\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Class RegisterController
 *
 * @package App\Http\Controllers\Auth
 */
final class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
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
        $this->middleware('guest');
        $this->usersRepository = $usersRepository;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function validator(array $data)
    {
        $table = (new User())->getTable();
        return Validator::make($data, [
            User::ATTR_FIRST_NAME => ['required', 'string', 'max:255'],
            User::ATTR_LAST_NAME => ['nullable', 'string', 'max:255'],
            User::ATTR_NICKNAME => ['required', 'string', 'max:255', 'unique:' . $table],
            User::ATTR_EMAIL => ['required', 'string', 'email', 'max:255', 'unique:' . $table],
            User::ATTR_PASSWORD => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data): User
    {
        return $this->usersRepository->create($data);
    }
}
