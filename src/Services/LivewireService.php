<?php

namespace Luischavez\Livewire\Extensions\Services;

use Illuminate\Contracts\View\View;
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
     * Run on initialized.
     *
     * @return void
     */
    public function initialize(): void
    {}

    /**
     * Run on component boot.
     *
     * @return void
     */
    public function boot(): void
    {}

    /**
     * Run on component hydrate.
     *
     * @return void
     */
    public function hydrate(): void
    {}

    /**
     * Run on component mount.
     *
     * @return void
     */
    public function mount(): void
    {}

    /**
     * Run when all services are mounted and hydrated.
     *
     * @return void
     */
    public function booted(): void
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
     * Run on component rendering.
     *
     * @return void
     */
    public function rendering(): void
    {}

    /**
     * Run on component rendered.
     *
     * @param View $view view
     * @return void
     */
    public function rendered(View $view): void
    {}

    /**
     * Run on component dehydrate.
     *
     * @return void
     */
    public function dehydrate(): void
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
