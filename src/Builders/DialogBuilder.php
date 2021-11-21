<?php

namespace Luischavez\Livewire\Extensions\Builders;

use Luischavez\Livewire\Extensions\Callback;
use Luischavez\Livewire\Extensions\Services\DialogService;
use Luischavez\Livewire\Extensions\Services\TaggingService;
use Luischavez\Livewire\Extensions\Widgets\Dialog;

/**
 * Dialog builder.
 */
class DialogBuilder
{
    /**
     * Dialog service.
     *
     * @var DialogService
     */
    protected DialogService $dialogService;

    /**
     * Dialog type.
     *
     * @var string
     */
    protected string $type = Dialog::DEFAULT;

    /**
     * Dialog title.
     *
     * @var string
     */
    protected string $title;

    /**
     * Dialog message.
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
     */
    protected $inputValue = null;

    /**
     * Dimissable flag.
     *
     * @var bool
     */
    protected bool $dimissable = false;

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
     * @param DialogService $dialogService  dialog service
     * @param string        $type           dialog type
     * @param string        $title          dialog title
     * @param string        $message        dialog message
     */
    protected function __construct(DialogService $dialogService, string $title, string $message)
    {
        $this->dialogService = $dialogService;
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
     * Set the dialog type.
     *
     * @param string $type dialog type
     * @return self
     */
    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the icon for the dialog.
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
     * Add an input to the dialog.
     *
     * @param string    $name       input name
     * @param     $value      input value
     * @param array     $options    input options
     * @return self
     */
    public function withInput(string $name, $value = null, array $options = []): self
    {
        $this->inputName = $name;
        $this->inputValue = $value;
        $this->inputOptions = $options;

        return $this;
    }

    /**
     * Enable dimiss button on the dialog.
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
     * Show dialog.
     *
     * @param string $spawner spawner tag name
     * @return void
     */
    public function show(string $spawner = 'dialogs'): void
    {
        $dialog = new Dialog();

        $dialog->tag = $this->tag ?? TaggingService::generateTag();

        $dialog->type = $this->type;
        $dialog->title = $this->title;
        $dialog->message = $this->message;
        $dialog->iconName = $this->iconName;
        $dialog->iconStyle = $this->iconStyle;
        $dialog->iconGroup = $this->iconGroup;
        $dialog->inputName = $this->inputName;
        $dialog->inputValue = $this->inputValue;
        $dialog->inputOptions = $this->inputOptions;
        $dialog->showCancelButton = $this->showCancelButton;
        $dialog->showConfirmButton = $this->showConfirmButton;
        $dialog->cancelText = $this->cancelText;
        $dialog->confirmText = $this->confirmText;
        $dialog->cancelCallback = $this->cancelCallback;
        $dialog->confirmCallback = $this->confirmCallback;
        $dialog->dimissable = $this->dimissable;

        $this->dialogService->show($dialog, $spawner);
    }

    /**
     * Creates a new builder.
     *
     * @param DialogService $dialogService  dialog service
     * @param string        $title          dialog title
     * @param string        $message        dialog message
     * @return self
     */
    public static function create(DialogService $dialogService, string $title, string $message): self
    {
        return new self($dialogService, $title, $message);
    }
}
