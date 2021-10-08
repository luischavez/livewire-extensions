<?php

namespace Luischavez\Livewire\Extensions\Services;

use Illuminate\Support\Str;
use Luischavez\Livewire\Extensions\Action;
use Luischavez\Livewire\Extensions\Exceptions\ActionException;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\InspectorQuery;
use Luischavez\Livewire\Extensions\Reflection\Method;
use Luischavez\Livewire\Extensions\TypeFinder;

/**
 * Action service
 */
class ActionService extends LivewireService
{
    /**
     * Gets an action.
     *
     * @param string $actionName
     * @return Action|null
     * 
     * @throws ActionException
     */
    public function getAction(string $actionName): ?Action
    {
        $actionClass = TypeFinder::find('actions', $actionName);

        if (!$actionClass) {
            return null;
        }

        if (!is_subclass_of($actionClass, Action::class)) {
            throw new ActionException("$actionName is not an action");
        }

        return app()->make($actionClass, ['component' => $this->component, 'name' => $actionName]);
    }

    /**
     * Notify the caller the action is completed.
     *
     * @param string    $actionName action name
     * @param array     $parameters action parameters
     * @param mixed     $result     action result
     * @return void
     */
    protected function notifyCaller(string $actionName, array $parameters, mixed $result): void
    {
        /**
         * @var Method|null
         */
        $method = Inspector::inspect($this->component)
            ->method()
            ->withName('handleActionCompleted')
            ->withModifiers(InspectorQuery::PROTECTED_MODIFIER)
            ->first();

        if ($method !== null) {
            $method->invoke($actionName, $parameters, $result);
        }
    }

    /**
     * Emit the action events after execution.
     *
     * @param string    $actionName action name
     * @param array     $parameters action parameters
     * @param mixed     $result     action result
     * @return void
     */
    protected function emitEvents(string $actionName, array $parameters, mixed $result): void
    {
        /**
         * @var TaggingService
         */
        $taggingService = TaggingService::of($this->component);

        $taggingService->emitEvent("actionExecuted", null, null, $actionName, $parameters, $result);

        $actionNameParts = explode('.', $actionName);

        $studlyActionName = '';
        foreach ($actionNameParts as $actionNamePart) {
            $studlyActionName .= Str::studly($actionNamePart);

            $taggingService->emitEvent("action{$studlyActionName}Executed", null, null, $actionName, $parameters, $result);
        }
    }

    /**
     * Executes an action.
     *
     * @param string    $actionName
     * @param mixed     ...$parameters
     * @return mixed
     * 
     * @throws ActionException
     */
    public function execute(string $actionName, mixed ...$parameters): mixed
    {
        $action = $this->getAction($actionName);

        if (!$action) {
            throw new ActionException("Action $actionName not found");
        }

        $action->setParameters($parameters);

        $result = $action->doExecute();

        if ($action->isCompleted()) {
            $this->notifyCaller($actionName, $parameters, $action->getResult());
            $this->emitEvents($actionName, $parameters, $action->getResult());
        }

        return $result;
    }

    /**
     * Handles the action callback event.
     *
     * @param string    $actionName action name
     * @param string    $event      event name
     * @param array     $parameters event parameters
     * @return mixed
     * 
     * @throws ActionException
     */
    public function handleActionCallbackEvent(string $actionName, string $event, array $parameters): mixed
    {
        $action = $this->getAction($actionName);

        if ($action === null) {
            throw new ActionException("Action $actionName not found");
        }

        $result = $action->onEvent($event, $parameters);

        if ($action->isCompleted()) {
            $this->notifyCaller($actionName, $action->getParameters(), $action->getResult());
            $this->emitEvents($actionName, $action->getParameters(), $action->getResult());
        }

        return $result;
    }
}
