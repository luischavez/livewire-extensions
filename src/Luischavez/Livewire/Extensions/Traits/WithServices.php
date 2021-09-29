<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\Property;
use Luischavez\Livewire\Extensions\Services\LivewireService;

/**
 * Enables services.
 */
trait WithServices
{
    /**
     * Registered services.
     * 
     * @var array
     */
    protected array $livewireServices = [];

    /**
     * Search all the registered services on this component.
     *
     * @return void
     */
    protected function lookupServices(): void
    {
        $services = Inspector::inspect($this)
            ->property()
            ->withType(LivewireService::class)
            ->all();

        /**
         * @var Property
         */
        foreach ($services as $service) {
            // Initialize the service instance if null.
            if ($service->value() === null) {
                $this->{$service->name()} = app()->make($service->type());
            }

            $this->livewireServices[$service->name()] = $this->{$service->name()};
        }
    }

    /**
     * Gets a service.
     *
     * @param string $class
     * @return LivewireService|null
     */
    protected function service(string $class): ?LivewireService
    {
        foreach ($this->livewireServices as $service) {
            if ($service instanceof $class) {
                return $service;
            }
        }

        return null;
    }

    /**
     * Initializes services.
     *
     * @return void
     */
    public function initializeWithServices(): void
    {
        $this->lookupServices();

        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->setup($this);
            $service->initialize();
        }
    }

    /**
     * Mount services.
     *
     * @return void
     */
    public function mountWithServices(): void
    {
        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->mount();
        }
    }

    /**
     * Hydrate services.
     *
     * @return void
     */
    public function hydrateWithServices(): void
    {
        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            // Running here because on livewire trait mount ran after hydrate.
            //$service->mount();
            $service->hydrate();
        }

        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->ready();
        }
    }

    /**
     * Dehydarte services.
     *
     * @return void
     */
    public function dehydrateWithServices(): void
    {
        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->dehydrate();
        }
    }

    /**
     * Run on property updating.
     *
     * @param string    $name
     * @param mixed     $value
     * @return void
     */
    public function updatingWithServices(string $name, mixed $value): void
    {
        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->updating($name, $value);
        }
    }

    /**
     * Run on property updated.
     *
     * @param string    $name
     * @param mixed     $value
     * @return void
     */
    public function updatedWithServices(string $name, mixed $value): void
    {
        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->updated($name, $value);
        }
    }
}
