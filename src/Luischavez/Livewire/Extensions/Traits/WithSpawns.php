<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Livewire\Component;
use Luischavez\Livewire\Extensions\Services\SpawnService;

/**
 * Enables spawns.
 */
trait WithSpawns
{
    /**
     * Spawn service.
     *
     * @var SpawnService
     */
    protected SpawnService $spawnService;

    /**
     * Spawn a component.
     *
     * @param string            $spawner    spawner name
     * @param Component|string  $component  component name or instance
     * @param array             $properties component properties
     * @return void
     */
    protected function spawn(string $spawner, Component|string $component, array $properties = []): void
    {
        $this->spawnService->spawn($spawner, $component, $properties);
    }
}
