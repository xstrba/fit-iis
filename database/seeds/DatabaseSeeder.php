<?php declare(strict_types=1);

use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function run()
    {
         $this->call(UserSeeder::class);
    }
}
