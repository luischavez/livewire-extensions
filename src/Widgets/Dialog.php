<?php

namespace Luischavez\Livewire\Extensions\Widgets;

use Illuminate\Contracts\View\View;
use Luischavez\Livewire\Extensions\Callback;
use Luischavez\Livewire\Extensions\ServiceComponent;

/**
 * Dialog widget.
 */
class Dialog extends ServiceComponent
{
    const DEFAULT = 'default';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const INFO = 'info';
    const DANGER = 'danger';

    /**
     * Type of this dialog.
     * 
     * @var string
     */
    public string $type = self::DEFAULT;

    /**
     * Title.
     * 
     * @var string
     */
    public string $title = '';

    /**
     * Message.
     * 
     * @var string
     */
    public string $message = '';

    /**
     * Icon name,
     *
     * @var string|null
     */
    public ?string $iconName = null;

    /**
     * Icon style.
     *
     * @var string|null
     */
    public ?string $iconStyle = null;

    /**
     * Icon group.
     *
     * @var string|null
     */
    public ?string $iconGroup = null;

    /**
     * Input model.
     * 
     * @var string|null
     */
    public ?string $inputName = null;

    /**
     * Input model options.
     * 
     * @var array
     */
    public array $inputOptions = [];

    /**
     * Input value.
     * 
     * @var mixed
     */
    public mixed $inputValue = null;

    /**
     * Input extra values.
     *
     * @var array
     */
    public array $inputExtra = [];

    /**
     * Cancel button visible state.
     *
     * @var boolean
     */
    public bool $showCancelButton = true;

    /**
     * Confirm button visible state.
     *
     * @var boolean
     */
    public bool $showConfirmButton = true;

    /**
     * Cancel button text.
     *
     * @var string|null
     */
    public ?string $cancelText = null;

    /**
     * Confirm button text.
     *
     * @var string|null
     */
    public ?string $confirmText = null;

    /**
     * Cancel callback.
     *
     * @var Callback|null
     */
    public ?Callback $cancelCallback = null;

    /**
     * Confirm callback.
     *
     * @var Callback|null
     */
    public ?Callback $confirmCallback = null;

    /**
     * Dimissable flag.
     *
     * @var boolean
     */
    public bool $dimissable = false;

    /**
     * Defines the protected properties.
     * 
     * @var array
     */
    protected array $protectedProperties = [
        'inputName',
        'inputOptions',
        'inputValue',
        'inputExtra',
        'dimissable',
    ];

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire-ext::widgets.dialog');
    }

    /**
     * Cancel the dialog.
     *
     * @return void
     */
    public function cancel(): void
    {
        if ($this->cancelCallback !== null) {
            if ($this->inputName !== null) {
                $this->cancelCallback->fire($this->inputValue, ...$this->inputExtra);
            } else {
                $this->cancelCallback->fire();
            }
        }
    }

    /**
     * Confirm the dialog.
     *
     * @return void
     */
    public function confirm(): void
    {
        if ($this->confirmCallback !== null) {
            if ($this->inputName !== null) {
                $this->confirmCallback->fire($this->inputValue, ...$this->inputExtra);
            } else {
                $this->confirmCallback->fire();
            }
        }
    }

    /**
     * Triggered on input event.
     *
     * @param mixed $value input value
     * @param mixed $extra extra values
     * @return void
     */
    public function onInput(mixed $value, mixed ...$extra): void
    {
        $this->inputValue = $value;
        $this->inputExtra = $extra;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'widgets-dialog';        
    }
}
