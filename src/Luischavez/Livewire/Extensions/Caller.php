<?php

namespace Luischavez\Livewire\Extensions;

use Livewire\Component;
use Luischavez\Livewire\Extensions\Builders\AlertBuilder;
use Luischavez\Livewire\Extensions\Builders\DialogBuilder;
use Luischavez\Livewire\Extensions\Services\AlertService;
use Luischavez\Livewire\Extensions\Services\DialogService;

/**
 * Caller base class.
 */
abstract class Caller
{
    /**
     * Related component.
     *
     * @return Component
     */
    public abstract function component(): Component;

    /**
     * Caller type.
     *
     * @return string
     */
    public abstract function type(): string;

    /**
     * Caller unique name.
     *
     * @return string
     */
    public abstract function name(): string;

    /**
     * Create a new alert.
     *
     * @param string $title     alert title
     * @param string $message   alert message
     * @return AlertBuilder
     */
    protected function alert(string $title, string $message): AlertBuilder
    {
        /**
         * @var AlertService
         */
        $alertService = AlertService::of($this->component());
        return $alertService->alert($title, $message);
    }

    /**
     * Create a new dialog.
     *
     * @param string $title     dialog title
     * @param string $message   dialog message
     * @return DialogBuilder
     */
    protected function dialog(string $title, string $message): DialogBuilder
    {
        /**
         * @var DialogService
         */
        $dialogService = DialogService::of($this->component());
        return $dialogService->dialog($title, $message);
    }
}
