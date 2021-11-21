<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Luischavez\Livewire\Extensions\Services\ActionService;

/**
 * Enables actions.
 */
trait WithActions
{
    /**
     * Action service.
     *
     * @var ActionService
     */
    protected ActionService $actionService;

    /**
     * Executes an action.
     *
     * @param string    $actionName
     * @param     ...$parameters
     * 
     */
    public function executeAction(string $actionName, ...$parameters)
    {
        return $this->actionService->execute($actionName, ...$parameters);
    }

    /**
     * Handle action completed.
     *
     * @param string    $actionName action name
     * @param array     $parameters action parameters
     * @param     $result     action result
     * @return void
     */
    protected function handleActionCompleted(string $actionName, array $parameters, $result): void
    {

    }

    /**
     * Handles action callbacks.
     *
     * @param string    $actionName action name
     * @param string    $event      event name
     * @param array     $parameters event parameters
     * 
     */
    protected function handleActionCallbackEvent(string $actionName, string $event, array $parameters)
    {
        return $this->actionService->handleActionCallbackEvent($actionName, $event, $parameters);
    }
}
