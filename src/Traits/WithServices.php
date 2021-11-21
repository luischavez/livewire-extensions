<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Illuminate\Contracts\View\View;
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

        $this->livewireServices = [];

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
     * Boot services.
     *
     * @return void
     */
    public function bootWithServices(): void
    {
        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->boot();
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
            $service->hydrate();
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
     * Run on component booted.
     *
     * @return void
     */
    public function bootedWithServices(): void
    {
        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->booted();
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

    /**
     * Run on component rendering.
     *
     * @return void
     */
    public function renderingWithServices(): void
    {
        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->rendering();
        }
    }

    /**
     * Run on component rendered.
     *
     * @param View $view view
     * @return void
     */
    public function renderedWithServices(View $view): void
    {
        /**
         * @var LivewireService
         */
        foreach ($this->livewireServices as $service) {
            $service->rendered($view);
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
}
