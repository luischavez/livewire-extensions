<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Luischavez\Livewire\Extensions\Builders\AlertBuilder;
use Luischavez\Livewire\Extensions\Services\AlertService;

/**
 * Enables alerts.
 */
trait WithAlerts
{
    /**
     * Alert service.
     * 
     * @var AlertService
     */
    protected AlertService $alertService;

    /**
     * Creates a new alert.
     *
     * @param string $title
     * @param string $message
     * @return AlertBuilder
     */
    protected function alert(string $title, string $message): AlertBuilder
    {
        return $this->alertService->alert($title, $message);
    }
}
