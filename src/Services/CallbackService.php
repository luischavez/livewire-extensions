<?php

namespace Luischavez\Livewire\Extensions\Services;

use Illuminate\Support\Str;
use Luischavez\Livewire\Extensions\Callback;
use Luischavez\Livewire\Extensions\Exceptions\CallbackException;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\InspectorQuery;
use Luischavez\Livewire\Extensions\Reflection\Method;
use Luischavez\Livewire\Extensions\Reflection\Property;

/**
 * Callback service.
 */
class CallbackService extends LivewireService
{
    /**
     * Defined callbacks.
     *
     * @var array
     */
    protected array $callbacks = [];

    /**
     * Search all callbacks on the component.
     *
     * @return void
     */
    protected function lookup(): void
    {
        $properties = Inspector::inspect($this->component)
            ->property()
            ->withType(Callback::class)
            ->withModifiers(InspectorQuery::PUBLIC_MODIFIER)
            ->all();

        $this->callbacks = [];

        /**
         * @var Property
         */
        foreach ($properties as $property) {
            /**
             * @var Callback|null
             */
            $callback = $property->value();

            if ($callback !== null) {
                $callback->setComponent($this->component);
            }

            $this->callbacks[] = $property->name();
        }
    }

    /**
     * Set a callback on the component.
     *
     * @param string    $callbackName
     * @param Callback  $callback
     * @return void
     */
    protected function setCallback(string $callbackName, Callback $callback): void
    {
        if (!in_array($callbackName, $this->callbacks)) {
            throw new CallbackException("Undefined callback $callbackName");
        }

        $this->component->$callbackName = $callback;
    }

    /**
     * Gets a callback from the component.
     *
     * @param string $callbackName
     * @return Callback|null
     */
    protected function getCallback(string $callbackName): ?Callback
    {
        if (!in_array($callbackName, $this->callbacks)) {
            return null;
        }

        return $this->component->$callbackName;
    }

    /**
     * Gets all the registered callbacks on the component.
     *
     * @return array
     */
    public function callbacks(): array
    {
        return $this->callbacks;
    }

    /**
     * Register a callback on this component or in other component.
     *
     * @param string        $callbackName
     * @param Callback      $callback
     * @param string|null   $tag
     * @param string|null   $component
     * @return void
     */
    public function register(string $callbackName, Callback $callback, ?string $tag = null, ?string $component): void
    {
        if ($tag) {
            /**
             * @var TaggingService
             */
            $taggingService = TaggingService::of($this->component);

            $taggingService->emitEvent('registerCallback', $tag, $component,
                $callbackName, $callback->toJavascript());
        } else {
            $this->setCallback($callbackName, $callback);
        }
    }

    /**
     * Handles the register callback event.
     *
     * @param string    $callbackName   callback name
     * @param     $callback       callback
     * @return void
     */
    public function handleRegisterEvent(string $callbackName, $callback): void
    {
        $callback = Callback::fromJavascript($callback);
        $callback->setComponent($this->component);

        $this->setCallback($callbackName, $callback);
    }

    /**
     * Handle a caller callback event.
     *
     * @param string    $type       caller type
     * @param string    $name       caller name
     * @param string    $event      event name
     * @param array     $parameters event parameters
     * 
     */
    public function handleCallerCallbackEvent(string $type, string $name, string $event, array $parameters)
    {
        $type = Str::studly($type);
        $methodName = "handle{$type}CallbackEvent";

        /**
         * @var Method|null
         */
        $method = Inspector::inspect($this->component)
            ->method()
            ->withName($methodName)
            ->withModifiers(InspectorQuery::PROTECTED_MODIFIER)
            ->first();

        if ($method === null) {
            throw new CallbackException("Undefined handler for callback of type $type [$methodName]");
        }

        return $method->invoke($name, $event, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->lookup();
    }
}
