<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Luischavez\Livewire\Extensions\Callback;
use Luischavez\Livewire\Extensions\Services\CallbackService;

/**
 * Enables callbacks.
 */
trait WithCallbacks
{
    /**
     * Callback service.
     *
     * @var CallbackService
     */
    protected CallbackService $callbackService;

    /**
     * Protected properties.
     *
     * @return array
     */
    protected function protectPropertiesWithCallbacks(): array
    {
        return $this->callbackService->callbacks();
    }

    /**
     * Register a callback on this component or in other component.
     *
     * @param string        $callbackName   callback name
     * @param Callback      $callback       callback
     * @param string|null   $tag            target tag
     * @param string|null   $component      target type
     * @return void
     */
    protected function registerCallback(string $callbackName, Callback $callback, ?string $tag = null, ?string $component): void
    {
        $this->callbackService->register($callbackName, $callback, $tag, $component);
    }

    /**
     * Run on register callback event.
     *
     * @param string    $callbackName   callback name
     * @param     $callback       callback
     * @return void
     */
    public function onRegisterCallback(string $callbackName, $callback): void
    {
        $this->callbackService->handleRegisterEvent($callbackName, $callback);
    }

    /**
     * Run on caller callback event.
     *
     * @param string    $type       caller type
     * @param string    $name       caller name
     * @param string    $event      event name
     * @param array     $parameters event parameters
     * 
     */
    public function onCallerCallback(string $type, string $name, string $event, array $parameters)
    {
        return $this->callbackService->handleCallerCallbackEvent($type, $name, $event, $parameters);
    }
}
