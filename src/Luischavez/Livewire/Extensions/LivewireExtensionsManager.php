<?php

namespace Luischavez\Livewire\Extensions;

use Livewire\Component;
use Livewire\LivewireManager;

/**
 * Livewire extensions manager.
 */
class LivewireExtensionsManager extends LivewireManager
{
    /**
     * Component instances.
     *
     * @var array
     */
    protected static array $instances = [];

    /**
     * @inheritDoc
     */
    public function getInstance($component, $id)
    {
        /**
         * @var Component
         */
        $instance = parent::getInstance($component, $id);

        self::$instances[$id] = $instance;

        return $instance;
    }

    /**
     * Gets all component instances.
     *
     * @return array
     */
    public function instances(): array
    {
        return self::$instances;
    }

    /**
     * Get the first component.
     *
     * @return Component|null
     */
    public function firstComponent(): ?Component
    {
        if (!empty(self::$instances)) {
            return array_values(self::$instances)[0];
        }

        return null;
    }

    /**
     * Get a component instance by id.
     *
     * @param string $id component id
     * @return Component|null
     */
    public function findInstance(string $id): ?Component
    {
        return self::$instances[$id] ?? null;
    }
}
