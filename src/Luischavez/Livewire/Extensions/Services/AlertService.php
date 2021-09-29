<?php

namespace Luischavez\Livewire\Extensions\Services;

use Luischavez\Livewire\Extensions\Builders\AlertBuilder;
use Luischavez\Livewire\Extensions\Widgets\Alert;

/**
 * Alert service.
 */
class AlertService extends LivewireService
{
    /**
     * Creates a new alert.
     *
     * @param string $title     alert title
     * @param string $message   alert message
     * @return AlertBuilder
     */
    public function alert(string $title, string $message): AlertBuilder
    {
        return AlertBuilder::create($this, $title, $message);
    }

    /**
     * Show the alert through the spawn system.
     *
     * @param Alert     $alert      alert instance
     * @param string    $spawner    spawner name
     * @return void
     */
    public function show(Alert $alert, string $spawner): void
    {
        /**
         * @var SpawnService
         */
        $spawnService = SpawnService::of($this->component);
        $spawnService->spawn($spawner, $alert);
    }
}
