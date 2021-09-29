<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Luischavez\Livewire\Extensions\Builders\DialogBuilder;
use Luischavez\Livewire\Extensions\Services\DialogService;

/**
 * Enables dialogs.
 */
trait WithDialogs
{
    /**
     * Dialog service.
     * 
     * @var DialogService
     */
    protected DialogService $dialogService;

    /**
     * Creates a new dialog.
     *
     * @param string $title
     * @param string $message
     * @return DialogBuilder
     */
    protected function dialog(string $title, string $message): DialogBuilder
    {
        return $this->dialogService->dialog($title, $message);
    }
}
