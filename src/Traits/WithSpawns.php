<?php

namespace Luischavez\Livewire\Extensions\Traits;

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
     * @param string    $spawner    spawner name
     * @param     $component  component name or instance
     * @param array     $properties component properties
     * @return void
     */
    protected function spawn(string $spawner, $component, array $properties = []): void
    {
        $this->spawnService->spawn($spawner, $component, $properties);
    }
}
