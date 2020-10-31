<?php declare(strict_types=1);

use App\Models\User;

/**
 * Class UserSeeder
 */
final class UserSeeder extends \Illuminate\Database\Seeder
{
    /**
     * @var \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     */
    private \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository;

    /**
     * @var \Illuminate\Contracts\Hashing\Hasher $hasher
     */
    private \Illuminate\Contracts\Hashing\Hasher $hasher;

    /**
     * UserSeeder constructor.
     *
     * @param \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     */
    public function __construct(
        \App\Contracts\Repositories\UsersRepositoryInterface $usersRepository,
        \Illuminate\Contracts\Hashing\Hasher $hasher
    ) {
        $this->usersRepository = $usersRepository;
        $this->hasher = $hasher;
    }

    /**
     * Create admin user
     *
     * @return void
     */
    public function run(): void
    {
        $user = $this->usersRepository->findByNickname('root');

        if (!$user) {
            $this->usersRepository->create([
                User::ATTR_FIRST_NAME => 'root',
                User::ATTR_EMAIL => 'root@email.com',
                User::ATTR_NICKNAME => 'root',
                User::ATTR_ROLE => \App\Enums\RolesEnum::ROLE_ADMINISTRATOR,
                User::ATTR_PASSWORD => $this->hasher->make('123456'),
                User::ATTR_BIRTH => \Carbon\Carbon::now()->subYears(40),
            ]);
        }
    }
}
