<?php

namespace Luischavez\Livewire\Extensions\Services;

use Luischavez\Livewire\Extensions\Builders\DialogBuilder;
use Luischavez\Livewire\Extensions\Widgets\Dialog;

/**
 * Dialog service.
 */
class DialogService extends LivewireService
{
    /**
     * Creates a new dialog.
     *
     * @param string $title     dialog title
     * @param string $message   dialog message
     * @return DialogBuilder
     */
    public function dialog(string $title, string $message): DialogBuilder
    {
        return DialogBuilder::create($this, $title, $message);
    }

    /**
     * Show the dialog through the spawn system.
     *
     * @param Dialog $dialog    dialog instance
     * @param string $spawner   spawner name
     * @return void
     */
    public function show(Dialog $dialog, string $spawner): void
    {
        /**
         * @var SpawnService
         */
        $spawnService = SpawnService::of($this->component);
        $spawnService->spawn($spawner, $dialog);
    }
}
