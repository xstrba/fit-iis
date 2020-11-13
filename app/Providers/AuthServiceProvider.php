<?php declare(strict_types=1);

namespace App\Providers;

use App\Http\Policies\GroupPolicy;
use App\Http\Policies\TestPolicy;
use App\Http\Policies\UserPolicy;
use App\Models\Group;
use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Class AuthServiceProvider
 *
 * @package App\Providers
 */
final class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var string[] $policies
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Test::class => TestPolicy::class,
        Group::class => GroupPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
