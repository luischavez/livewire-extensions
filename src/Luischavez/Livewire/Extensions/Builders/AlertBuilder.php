<?php

namespace Luischavez\Livewire\Extensions\Builders;

use Luischavez\Livewire\Extensions\Callback;
use Luischavez\Livewire\Extensions\Services\AlertService;
use Luischavez\Livewire\Extensions\Services\TaggingService;
use Luischavez\Livewire\Extensions\Widgets\Alert;

/**
 * Alert builder.
 */
class AlertBuilder
{
    /**
     * Alert service.
     *
     * @var AlertService
     */
    protected AlertService $alertService;

    /**
     * Alert type.
     *
     * @var string
     */
    protected string $type = Alert::DEFAULT;

    /**
     * Alert title.
     *
     * @var string
     */
    protected string $title;

    /**
     * Alert message.
     *
     * @var string
     */
    protected string $message;

    /**
     * Tag.
     *
     * @var string|null
     */
    protected ?string $tag = null;

    /**
     * Icon name.
     *
     * @var string|null
     */
    protected ?string $iconName = null;

    /**
     * Icon style.
     *
     * @var string|null
     */
    protected ?string $iconStyle = null;

    /**
     * Icon group.
     *
     * @var string|null
     */
    protected ?string $iconGroup = null;

    /**
     * Input name.
     *
     * @var string|null
     */
    protected ?string $inputName = null;

    /**
     * Input options.
     *
     * @var array
     */
    protected array $inputOptions = [];

    /**
     * Input value.
     *
     * @var mixed
     */
    protected mixed $inputValue = null;

    /**
     * Close the alert after seconds elapsed.
     *
     * @var mixed
     */
    protected mixed $closeAfter = null;

    /**
     * Dimissable flag.
     *
     * @var mixed
     */
    protected mixed $dimissable = null;

    /**
     * Cancel button visible status.
     *
     * @var bool
     */
    protected bool $showCancelButton = false;

    /**
     * Confirm button visible status.
     *
     * @var bool
     */
    protected bool $showConfirmButton = false;

    /**
     * Cancel button text.
     *
     * @var string|null
     */
    protected ?string $cancelText = null;

    /**
     * Confirm button text.
     *
     * @var string|null
     */
    protected ?string $confirmText = null;

    /**
     * Cancel callback.
     *
     * @var Callback|null
     */
    protected ?Callback $cancelCallback = null;

    /**
     * Confirm callback.
     *
     * @var Callback|null
     */
    protected ?Callback $confirmCallback = null;

    /**
     * Parameters to prepend in callbacks.
     *
     * @var array
     */
    protected array $prependParametersToCallbacks = [];

    /**
     * Parameters to append in callbacks.
     *
     * @var array
     */
    protected array $appendParametersToCallbacks = [];

    /**
     * Constructor
     *
     * @param AlertService  $alertService   alert service
     * @param string        $type           alert type
     * @param string        $title          alert title
     * @param string        $message        alert message
     */
    protected function __construct(AlertService $alertService, string $title, string $message)
    {
        $this->alertService = $alertService;
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Prepends parameters in callbacks.
     *
     * @param string    $type           callback type
     * @param array     ...$parameters  parameters
     * @return void
     */
    public function prependParameters(string $type, ...$parameters): void
    {
        $this->prependParametersToCallbacks[$type] = $parameters;
    }

    /**
     * Appends parameters in callbacks.
     *
     * @param string    $name           callback type
     * @param array     ...$parameters  parameters
     * @return void
     */
    public function appendParameters(string $type, ...$parameters): void
    {
        $this->appendParametersToCallbacks[$type] = $parameters;
    }

    /**
     * Set the dialog tag.
     *
     * @param string $tag
     * @return self
     */
    public function tag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Set the alert type.
     *
     * @param string $type alert type
     * @return self
     */
    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the icon for the alert.
     *
     * @param string        $name   icon name
     * @param string|null   $style  icon style
     * @param string|null   $group  icon group
     * @return self
     */
    public function withIcon(string $name, ?string $style = null, ?string $group = null): self
    {
        $this->iconName = $name;
        $this->iconStyle = $style;
        $this->iconGroup = $group;

        return $this;
    }

    /**
     * Add an input to the alert.
     *
     * @param string    $name       input name
     * @param mixed     $value      input value
     * @param array     $options    input options
     * @return self
     */
    public function withInput(string $name, mixed $value = null, array $options = []): self
    {
        $this->inputName = $name;
        $this->inputValue = $value;
        $this->inputOptions = $options;

        return $this;
    }

    /**
     * Close the alert after seconds elapsed.
     *
     * @param integer $seconds seconds
     * @return self
     */
    public function closeAfter(int $seconds): self
    {
        $this->closeAfter = $seconds;

        return $this;
    }

    /**
     * Enable dimiss button on the alert.
     *
     * @return self
     */
    public function dimissable(): self
    {
        $this->dimissable = true;

        return $this;
    }

    /**
     * Shows the cancel button.
     *
     * @return self
     */
    public function showCancelButton(): self
    {
        $this->showCancelButton = true;

        return $this;
    }

    /**
     * Shows the confirm button.
     *
     * @return self
     */
    public function showConfirmButton(): self
    {
        $this->showConfirmButton = true;

        return $this;
    }

    /**
     * Sets the cancel button text.
     *
     * @param string $text cancel text
     * @return self
     */
    public function cancelText(string $text): self
    {
        $this->cancelText = $text;

        return $this;
    }

    /**
     * Sets the confirm button text.
     *
     * @param string $text confirm text
     * @return self
     */
    public function confirmText(string $text): self
    {
        $this->confirmText = $text;

        return $this;
    }

    /**
     * Fix the callback to add the extra parameters.
     *
     * @param array $callbacks array of callbacks
     * @return Callback
     */
    protected function fixCallback(Callback $callback): Callback
    {
        $routes = $callback->routes();

        foreach ($routes as &$route) {
            foreach ($this->prependParametersToCallbacks as $type => $parameters) {
                if ($route['type'] == $type) {
                    $route['parameters'][3] = array_merge($parameters, $route['parameters'][3]);
                }
            }
            foreach ($this->appendParametersToCallbacks as $type => $parameters) {
                if ($route['type'] == $type) {
                    $route['parameters'][3] = array_merge($route['parameters'], $parameters[3]);
                }
            }
        }

        $callbackData = $callback->toJavascript();
        $callbackData['routes'] = $routes;

        return Callback::fromJavascript($callbackData);
    }

    /**
     * Set the cancel callback.
     *
     * @param Callback $callback callback
     * @return self
     */
    public function onCancel(Callback $callback): self
    {
        $this->cancelCallback = $this->fixCallback($callback);
        $this->showCancelButton();

        return $this;
    }

    /**
     * Set the confirm callback.
     *
     * @param Callback $callback callback
     * @return self
     */
    public function onConfirm(Callback $callback): self
    {
        $this->confirmCallback = $this->fixCallback($callback);
        $this->showConfirmButton();

        return $this;
    }

    /**
     * Show alert.
     *
     * @param string $spawner spawner tag name
     * @return void
     */
    public function show(string $spawner = 'alerts'): void
    {
        $alert = new Alert();

        $alert->tag = $this->tag ?? TaggingService::generateTag();

        $alert->type = $this->type;
        $alert->title = $this->title;
        $alert->message = $this->message;
        $alert->iconName = $this->iconName ?? config("livewire-ext.alerts.options.{$this->type}.icon.name");
        $alert->iconStyle = $this->iconStyle ?? config("livewire-ext.alerts.options.{$this->type}.icon.style");
        $alert->iconGroup = $this->iconGroup ?? config("livewire-ext.alerts.options.{$this->type}.icon.group");
        $alert->inputName = $this->inputName;
        $alert->inputValue = $this->inputValue;
        $alert->inputOptions = $this->inputOptions;
        $alert->showCancelButton = $this->showCancelButton ?? config("livewire-ext.alerts.options.{$this->type}.show_cancel");
        $alert->showConfirmButton = $this->showConfirmButton ?? config("livewire-ext.alerts.options.{$this->type}.show_confirm");
        $alert->cancelText = $this->cancelText ?? config("livewire-ext.alerts.options.{$this->type}.cancel_text");
        $alert->confirmText = $this->confirmText ?? config("livewire-ext.alerts.options.{$this->type}.confirm_text");
        $alert->cancelCallback = $this->cancelCallback;
        $alert->confirmCallback = $this->confirmCallback;
        $alert->closeAfter = $this->closeAfter ?? config("livewire-ext.alerts.options.{$this->type}.close_after", 0);
        $alert->dimissable = $this->dimissable ?? config("livewire-ext.alerts.options.{$this->type}.dimissable", false);

        $this->alertService->show($alert, $spawner);
    }

    /**
     * Creates a new builder.
     *
     * @param AlertService  $alertService   alert service
     * @param string        $title          alert title
     * @param string        $message        alert message
     * @return self
     */
    public static function create(AlertService $alertService, string $title, string $message): self
    {
        return new self($alertService, $title, $message);
    }
}
