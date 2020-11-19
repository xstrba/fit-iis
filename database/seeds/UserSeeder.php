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
        $faker = \Faker\Factory::create('cs');

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

        foreach (range(1,10) as $i) {
            $role = \App\Enums\RolesEnum::ROLE_STUDENT;
            if ($i < 3) {
                $role = \App\Enums\RolesEnum::ROLE_PROFESSOR;
                $date = $faker->dateTimeBetween('-70 years', '-30 years');
            } else if ($i < 6) {
                $role = \App\Enums\RolesEnum::ROLE_ASSISTANT;
                $date = $faker->dateTimeBetween('-50 years', '-25 years');
            } else {
                $date = $faker->dateTimeBetween('-50 years', '-18 years');
            }

            $this->usersRepository->create([
                User::ATTR_FIRST_NAME => $faker->firstName,
                User::ATTR_LAST_NAME => $faker->lastName,
                User::ATTR_EMAIL => $faker->email,
                User::ATTR_NICKNAME => $faker->unique()->userName,
                User::ATTR_ROLE => $role,
                User::ATTR_PASSWORD => $this->hasher->make('123456'),
                User::ATTR_BIRTH => $date,
                User::ATTR_STREET => $faker->streetName,
                User::ATTR_HOUSE_NUMBER => $faker->buildingNumber,
                User::ATTR_CITY => $faker->city,
                User::ATTR_COUNTRY => $faker->randomElement(\App\Enums\CountriesEnum::instance()->getValues()),
                User::ATTR_GENDER => $faker->randomElement(\App\Enums\GendersEnum::instance()->getValues()),
                User::ATTR_LANGUAGE => \App\Enums\LanguagesEnum::CZ,
                User::ATTR_PHONE => $faker->phoneNumber,
            ]);
        }
    }
}
