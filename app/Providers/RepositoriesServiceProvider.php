<?php declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\TestsRepositoryInterface;
use App\Contracts\Repositories\UsersRepositoryInterface;
use App\Repositories\TestsRepository;
use App\Repositories\UsersRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoriesServiceProvider
 *
 * @package App\Providers
 */
final class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function register()
    {
        $this->app->bind(UsersRepositoryInterface::class, UsersRepository::class);
        $this->app->bind(TestsRepositoryInterface::class, TestsRepository::class);
    }
}
