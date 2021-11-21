<?php

namespace Luischavez\Livewire\Extensions;

use Illuminate\Support\Str;
use Livewire\Component;
use Luischavez\Livewire\Extensions\Builders\AlertBuilder;
use Luischavez\Livewire\Extensions\Builders\DialogBuilder;
use Luischavez\Livewire\Extensions\Exceptions\ActionException;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\InspectorQuery;
use Luischavez\Livewire\Extensions\Widgets\Alert;
use Throwable;

/**
 * Action.
 */
abstract class Action extends Caller
{
    /**
     * Caller component.
     *
     * @var Component
     */
    protected Component $component;

    /**
     * Action parameters.
     *
     * @var array
     */
    protected array $parameters = [];

    /**
     * Notify action completed.
     *
     * @var boolean
     */
    protected bool $notify = false;

    /**
     * Notify result.
     */
    protected $notifyResult = null;

    /**
     * Constructor.
     *
     * @param Component $component  component
     * @param array     $parameters parameters
     */
    public function __construct(Component $component, array $parameters = [])
    {
        $this->component = $component;
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function component(): Component
    {
        return $this->component;
    }

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return 'action';
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return TypeFinder::name('actions', static::class);
    }
    
    /**
     * Notify action completed.
     *
     * @param $result result
     * @return void
     */
    protected function notify($result): void
    {
        $this->notify = true;
        $this->notifyResult = $result;
    }

    /**
     * Check if action completed.
     *
     * @return boolean
     */
    public function isCompleted(): bool
    {
        return $this->notify;
    }

    /**
     * Gets the result.
     */
    public function getResult()
    {
        return $this->notifyResult;
    }

    /**
     * Sets the parameters.
     *
     * @param array $parameters parameters
     * @return void
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Gets the parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    protected function alert(string $title, string $message): AlertBuilder
    {
        $builder = parent::alert($title, $message);

        $builder->prependParameters($this->type(), $this->parameters);

        return $builder;
    }

    /**
     * @inheritDoc
     */
    protected function dialog(string $title, string $message): DialogBuilder
    {
        $builder = parent::dialog($title, $message);

        $builder->prependParameters($this->type(), $this->parameters);

        return $builder;
    }

    /**
     * Check if the action can be executed.
     *
     * @return boolean
     */
    protected function checkPermissions(): bool
    {
        if (method_exists($this, 'can')) {
            if (!$this->can(auth()->user(), ...$this->parameters)) {
                $this->alert('UNAUTHORIZED_TITLE', 'UNAUTHORIZED_MESSAGE')
                    ->type(Alert::DANGER)
                    ->dimissable()
                    ->closeAfter(5)
                    ->show();
                return false;
            }
        }

        return true;
    }

    /**
     * Create a new action exception.
     *
     * @param string            $message    exception message
     * @param integer           $code       exception code
     * @param Throwable|null    $previous   previous exception
     * @return ActionException
     */
    protected function exception(string $message = "", int $code = 0, ?Throwable $previous = null): ActionException
    {
        return new ActionException($message, $code, $previous);
    }

    /**
     * Handles events on this action.
     *
     * @param string    $event      event name
     * @param array     $parameters parameters
     */
    public function onEvent(string $event, array $parameters)
    {
        $methodName = 'on'.Str::studly($event);

        $method = Inspector::inspect($this)
            ->method()
            ->withName($methodName)
            ->withModifiers(InspectorQuery::PUBLIC_MODIFIER)
            ->first();

        if ($method === null) {
            throw new ActionException("Method $methodName not found or is not public");
        }

        $this->parameters = $parameters[0];
        unset($parameters[0]);

        if (!$this->checkPermissions()) {
            return null;
        }

        return $this->$methodName(...$parameters);
    }

    /**
     * Handles the action execution.
     */
    public function doExecute()
    {
        if (!$this->checkPermissions()) {
            return null;
        }

        if (method_exists($this, 'execute')) {
            return $this->execute(...$this->parameters);
        }
    }
}
