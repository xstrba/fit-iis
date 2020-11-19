<?php declare(strict_types=1);

namespace App\Services;

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Models\Test;
use App\Models\User;
use App\Sidebar\Sidebar;
use App\Sidebar\SidebarItem;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;

/**
 * Class SidebarService
 *
 * @package App\Services
 */
final class SidebarService
{
    public const ITEM_TESTS = 'tests';
    public const ITEM_USERS = 'users';
    public const ITEM_PROFILE = 'profile';
    public const ITEM_MY_TESTS = 'my_tests';

    /**
     * @var \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     */
    private UrlGenerator $urlGenerator;

    /**
     * @var \App\Services\AuthService $authService
     */
    private AuthService $authService;

    /**
     * SidebarService constructor.
     *
     * @param \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator
     * @param \App\Services\AuthService $authService
     */
    public function __construct(
        \Illuminate\Contracts\Routing\UrlGenerator $urlGenerator,
        \App\Services\AuthService $authService
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->authService = $authService;
    }

    /**
     * Create and return sidebar with all items for given request
     *
     * @param string|null $activeItem
     * @return \App\Sidebar\Sidebar
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getSidebar(?string $activeItem = null): Sidebar
    {
        $user = $this->authService->user();
        $sidebar = new Sidebar();

        if ($user->role < RolesEnum::ROLE_ADMINISTRATOR) {
            $sidebar->addItem(new SidebarItem(
                self::ITEM_MY_TESTS,
                'tasks',
                $this->urlGenerator->route('tests.my'),
                    self::ITEM_MY_TESTS === $activeItem,
                ));
        }

        if ($user->can(PermissionsEnum::SHOW, Test::class)) {
            $sidebar->addItem(new SidebarItem(
                self::ITEM_TESTS,
                'tasks',
                $this->urlGenerator->route('tests.index'),
                self::ITEM_TESTS === $activeItem,
            ));
        }

        if ($user->can(PermissionsEnum::SHOW, User::class)) {
            $sidebar->addItem(new SidebarItem(
                self::ITEM_USERS,
                'users',
                $this->urlGenerator->route('users.index'),
                self::ITEM_USERS === $activeItem,
            ));
        }

        $sidebar->addItem(new SidebarItem(
            self::ITEM_PROFILE,
            'user',
            $this->urlGenerator->route('profile'),
            self::ITEM_PROFILE === $activeItem,
        ));

        return $sidebar;
    }
}
