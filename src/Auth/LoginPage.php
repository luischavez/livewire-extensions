<?php

namespace Luischavez\Livewire\Extensions\Auth;

use Illuminate\Contracts\View\View;
use Luischavez\Livewire\Extensions\Services\AuthService;
use Luischavez\Livewire\Extensions\Widgets\Alert;

/**
 * Login page.
 */
class LoginPage extends AuthPage
{
    /**
     * User email.
     * 
     * @var string
     */
    public string $email = '';

    /**
     * User password.
     * 
     * @var string
     */
    public string $password = '';

    /**
     * Remember the session.
     * 
     * @var bool
     */
    public bool $remember = false;

    /**
     * Validation rules.
     * 
     * @var array
     */
    public array $rules = [
        'email'     => 'required|email',
        'password'  => 'required',
        'remember'  => 'sometimes',
    ];

    /**
     * @inheritDoc
     */
    public function render(): ?View
    {
        return view('livewire-ext::auth.login');
    }

    /**
     * @inheritDoc
     */
    public function execute(): bool
    {
        $this->validate();

        /**
         * @var AuthService
         */
        $authService = AuthService::of($this->component);

        $logged = $authService->login([
            'email'     => $this->email,
            'password'  => $this->password,
        ], $this->remember);

        if ($logged) {
            $this->alert(
                trans('livewire-ext::auth.pages.login.title_success', auth()->user()->toArray()),
                trans('livewire-ext::auth.pages.login.message_success', auth()->user()->toArray()))
                ->type(Alert::SUCCESS)
                ->show();
        } else {
            $this->alert(
                trans('livewire-ext::auth.pages.login.title_error'),
                trans('livewire-ext::auth.pages.login.message_error'))
                ->type(Alert::DANGER)
                ->show();
        }

        return $logged;
    }
}
