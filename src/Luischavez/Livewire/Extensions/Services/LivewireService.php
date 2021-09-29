<?php

namespace Luischavez\Livewire\Extensions\Services;

use Livewire\Component;

/**
 * Base Livewire service.
 */
abstract class LivewireService
{
    /**
     * All livewire services.
     *
     * @var array
     */
    protected static array $services = [];

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

        self::$services[$component->id][static::class] = $this;
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

        if (isset(static::$services[$component->id][$type])) {
            return static::$services[$component->id][$type];
        }

        if ($initialize) {
            /**
             * @var LivewireService
             */
            $service = app()->make($type);
            $service->setup($component);

            return $service;
        }

        return null;
    }
}
