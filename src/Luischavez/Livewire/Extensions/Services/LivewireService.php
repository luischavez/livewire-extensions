<?php

namespace Luischavez\Livewire\Extensions\Services;

use Livewire\Component;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\Property;

/**
 * Base Livewire service.
 */
abstract class LivewireService
{
    /**
     * Related component of this service.
     *
     * @var Component
     */
    protected Component $component;

    /**
     * Setups this service and set the related component.
     *
     * @param Component $component
     * @return void
     */
    public function setup(Component $component): void
    {
        $this->component = $component;
    }

    /**
     * Run on component initialized.
     *
     * @return void
     */
    public function initialize(): void
    {}

    /**
     * Run on component mount.
     *
     * @return void
     */
    public function mount(): void
    {}

    /**
     * Run on component hydrate.
     *
     * @return void
     */
    public function hydrate(): void
    {}

    /**
     * Run when all services mounted and hydated.
     *
     * @return void
     */
    public function ready(): void
    {

    }

    /**
     * Run on component dehydrate.
     *
     * @return void
     */
    public function dehydrate(): void
    {}

    /**
     * Run on component property updating.
     *
     * @param string    $key
     * @param mixed     $value
     * @return void
     */
    public function updating(string $key, mixed $value): void
    {}

    /**
     * Run on component property updated.
     *
     * @param string    $key
     * @param mixed     $value
     * @return void
     */
    public function updated(string $key, mixed $value): void
    {}

    /**
     * Gets the component.
     *
     * @return Component component
     */
    public function component(): Component
    {
        return $this->component;
    }

    /**
     * Gets the service instance from the component or null if the service
     * is not registered on the component.
     *
     * @param Component $component  component
     * @param bool      $initialize initializes the service if not exists
     * @return static|null
     */
    public static function of(Component $component, bool $initialize = false): ?static
    {
        $type = static::class;

        /**
         * @var Property|null
         */
        $service = Inspector::inspect($component)
            ->property()
            ->withType($type)
            ->first();

        if ($service !== null && $service->value() !== null) {
            return $service->value();
        }

        if ($initialize && $service !== null) {
            /**
             * @var LivewireService
             */
            $instance = app()->make($type);
            $instance->setup($component);

            $service->set($instance);

            return $instance;
        }

        return null;
    }
}
