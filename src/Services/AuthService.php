<?php

namespace Luischavez\Livewire\Extensions\Services;

use Illuminate\Support\Arr;

/**
 * Auth service.
 */
class AuthService extends LivewireService
{
    /**
     * Emits the user logged event.
     *
     * @return void
     */
    protected function emitLogged(): void
    {
        $this->component->emit('userLogged');
    }

    /**
     * Emits the user logout event.
     *
     * @return void
     */
    protected function emitLogout(): void
    {
        $this->component->emit('userLogout');
    }

    /**
     * Triggers the logged event if the user is logged.
     *
     * @return void
     */
    public function triggerLoggedEvent(): void
    {
        if (auth()->check()) {
            $this->emitLogged();
        }
    }

    /**
     * Triggers the logout event if the user is not logged.
     *
     * @return void
     */
    public function triggerLogoutEvent(): void
    {
        if (!auth()->check()) {
            $this->emitLogout();
        }
    }

    /**
     * Verifies and emit the auth events.
     *
     * @return void
     */
    public function verifyAuth(): void
    {
        if (auth()->check()) {
            $this->triggerLoggedEvent();
        } else {
            $this->triggerLogoutEvent();
        }
    }

    /**
     * Filters the user data.
     *
     * @param mixed     $user       user instance
     * @param array     $hidden     hidden user data
     * @param array     $visible    visible user data
     * @return array
     */
    public function filterUserData(mixed $user, array $hidden, array $visible): array
    {
        if ($user === null) {
            return [];
        }

        $data = $user->toArray();
        $data = Arr::except($data, $hidden);
        $data = Arr::only($data, $visible);

        return $data;
    }

    /**
     * Set the logged user.
     *
     * @param mixed $user user
     * @return void
     */
    public function setUser(mixed $user): void
    {
        auth()->loginUsingId($user->id, true);
        $this->emitLogged();
    }

    /**
     * Try to login.
     *
     * @param array     $credentials    user credentials
     * @param boolean   $remember       remember user
     * @return boolean
     */
    public function login(array $credentials, bool $remember = false): bool
    {
        if (auth()->attempt($credentials, $remember)) {
            //request()->session()->regenerate();
            $this->verifyAuth();
            return true;
        }

        return false;
    }

    /**
     * Logout the user.
     *
     * @return void
     */
    public function logout(): void
    {
        auth()->logout();
        $this->verifyAuth();
    }

    /**
     * @inheritDoc
     */
    public function mount(): void
    {
        if (auth()->check()) {
            $this->component->onUserLogged();
        } else {
            $this->component->onUserLogout();
        }
    }
}
