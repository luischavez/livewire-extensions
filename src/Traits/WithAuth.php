<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Luischavez\Livewire\Extensions\Services\AuthService;

/**
 * Enables auth.
 */
trait WithAuth
{
    /**
     * Auth service.
     *
     * @var AuthService
     */
    protected AuthService $authService;

    /**
     * Hidden user data.
     *
     * @var array
     */
    protected array $hiddenUserData = [];

    /**
     * Visible user data.
     *
     * @var array
     */
    protected array $visibleUserData = ['name', 'email'];

    /**
     * User data.
     * 
     * @var array
     */
    public array $userData = [];

    /**
     * Run on auth status changed.
     *
     * @param mixed $user logged user in case is logged
     * @return void
     */
    protected function handleAuthStatusChanged(mixed $user): void
    {
        
    }

    /**
     * Login.
     *
     * @param array     $credentials    user credentials
     * @param boolean   $remember       remember user
     * @return boolean
     */
    protected function login(array $credentials, bool $remember = false): bool
    {
        return $this->authService->login($credentials, $remember);
    }

    /**
     * Logout.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->authService->logout();
    }

    /**
     * Gets the current logged user.
     *
     * @return mixed
     */
    public function getUserProperty(): mixed
    {
        return auth()->user();
    }

    /**
     * Gets the user data.
     *
     * @return array
     */
    public function getUserData(): array
    {
        return $this->userData;
    }

    /**
     * Refresh the auth data.
     *
     * @return void
     */
    public function refreshAuth(): void
    {
        if (auth()->check()) {
            $this->userData = $this->authService->filterUserData(
                auth()->user(),
                $this->hiddenUserData,
                $this->visibleUserData);
        } else {
            $this->userData = [];
        }
    }

    /**
     * Run on user logged event.
     *
     * @return void
     */
    public function onUserLogged(): void
    {
        if (auth()->check()) {
            $this->userData = $this->authService->filterUserData(
                auth()->user(),
                $this->hiddenUserData,
                $this->visibleUserData);
            $this->handleAuthStatusChanged(auth()->user());
        }
    }

    /**
     * Run on user logout event.
     *
     * @return void
     */
    public function onUserLogout(): void
    {
        if (!auth()->check()) {
            $this->userData = [];
            $this->handleAuthStatusChanged(null);
        }
    }
}
