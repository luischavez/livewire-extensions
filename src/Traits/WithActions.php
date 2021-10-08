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
     * @param mixed     ...$parameters
     * @return mixed
     */
    public function executeAction(string $actionName, mixed ...$parameters): mixed
    {
        return $this->actionService->execute($actionName, ...$parameters);
    }

    /**
     * Handle action completed.
     *
     * @param string    $actionName action name
     * @param array     $parameters action parameters
     * @param mixed     $result     action result
     * @return void
     */
    protected function handleActionCompleted(string $actionName, array $parameters, mixed $result): void
    {

    }

    /**
     * Handles action callbacks.
     *
     * @param string    $actionName action name
     * @param string    $event      event name
     * @param array     $parameters event parameters
     * @return mixed
     */
    protected function handleActionCallbackEvent(string $actionName, string $event, array $parameters): mixed
    {
        return $this->actionService->handleActionCallbackEvent($actionName, $event, $parameters);
    }
}
