<?php declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthManager;

/**
 * Class AuthService
 *
 * @package App\Services
 */
final class AuthService
{
    /**
     * @var \Illuminate\Auth\AuthManager $authManager
     */
    private AuthManager $authManager;

    /**
     * AuthService constructor.
     *
     * @param \Illuminate\Auth\AuthManager $authManager
     */
    public function __construct(\Illuminate\Auth\AuthManager $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * Get authenticated user
     *
     * @return \App\Models\User
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function user(): User
    {
        $user = $this->tryUser();
        if (!$user) {
            throw new AuthorizationException('You must log in first');
        }
        return $user;
    }

    /**
     * Get authenticated users id
     *
     * @return int
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getId(): int
    {
        $id = $this->tryId();
        if (!$id) {
            throw new AuthorizationException('You must log in first');
        }
        return $id;
    }

    /**
     * Get user if authenticated
     *
     * @return \App\Models\User|null
     */
    public function tryUser(): ?User
    {
        /** @var \App\Models\User|null $user */
        $user = $this->authManager->guard()->user();
        return $user;
    }

    /**
     * Get authenticated's id if exists
     *
     * @return int|null
     */
    public function tryId(): ?int
    {
        return $this->authManager->guard()->id();
    }
}
