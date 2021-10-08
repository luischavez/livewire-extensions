<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Luischavez\Livewire\Extensions\Services\ListenerService;

/**
 * Enables declarative listeners.
 */
trait WithDeclarativeListeners
{
    /**
     * Listener service.
     *
     * @var ListenerService
     */
    protected ListenerService $listenerService;

    /**
     * Listeners.
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * @inheritDoc
     */
    protected function getListeners()
    {
        return $this->listenerService->merge($this->listeners);
    }
}
