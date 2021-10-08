<?php

namespace Luischavez\Livewire\Extensions\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Luischavez\Livewire\Extensions\Services\AuthService;
use Luischavez\Livewire\Extensions\Widgets\Alert;
use Throwable;

/**
 * Register page.
 */
class RegisterPage extends AuthPage
{
    /**
     * User name.
     * 
     * @var string
     */
    public string $name = '';

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
     * Password confirmation.
     * 
     * @var string
     */
    public string $password_confirmation = '';

    /**
     * Validation rules.
     * 
     * @var array
     */
    public array $rules = [
        'name'                     => 'required|alpha_dash|min:6|max:24',
        'email'                    => 'required|email|unique:users,email',
        'password'                 => 'required|confirmed',
        'password_confirmation'    => 'required',
    ];

    /**
     * @inheritDoc
     */
    public function render(): ?View
    {
        return view('livewire-ext::auth.register');
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function updated(string $key, mixed $value): void
    {
        parent::updated($key, $value);

        if (in_array($key, ['password', 'password_confirmation'])) {
            $this->validateOnly('password');
            $this->validateOnly('password_confirmation');
        }
    }

    /**
     * @inheritDoc
     */
    public function execute(): bool
    {
        $this->validate();

        $model = config('livewire-ext.auth.model');

        /**
         * @var AuthService
         */
        $authService = AuthService::of($this->component);

        try {
            $user = new $model();
            $user->name = $this->name;
            $user->email = $this->email;
            $user->password = Hash::make($this->password);
            $user->save();

            $this->alert(
                trans('livewire-ext::auth.pages.register.title_success', $user->toArray()),
                trans('livewire-ext::auth.pages.register.message_success', $user->toArray()))
                ->type(Alert::SUCCESS)
                ->show();
        } catch (Throwable $ex) {
            Log::error($ex);

            $this->alert(
                trans('livewire-ext::auth.pages.register.title_error'),
                trans('livewire-ext::auth.pages.register.message_error'))
                ->type(Alert::DANGER)
                ->show();
        }

        $authService->setUser($user);

        return true;
    }
}
