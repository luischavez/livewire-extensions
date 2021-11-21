<?php

namespace Luischavez\Livewire\Extensions\Auth;

use Luischavez\Livewire\Extensions\Proxy;

/**
 * Auth page.
 */
abstract class AuthPage extends Proxy
{
    /**
     * Executes the action.
     *
     * @return bool
     */
    public abstract function execute(): bool;

    /**
     * @inheritDoc
     */
    protected function getValidationAttributes(): array
    {
        return trans('livewire-ext::auth.attributes');
    }

    /**
     * @inheritDoc
     */
    public function updated(string $key, $value): void
    {
        $this->resetErrorBag($key);
        $this->validateOnly($key);
    }
}
