<?php

namespace Luischavez\Livewire\Extensions\Widgets;

use Illuminate\Contracts\View\View;
use Luischavez\Livewire\Extensions\Callback;
use Luischavez\Livewire\Extensions\ProxyComponent;

/**
 * Smart input widget.
 */
class SmartInput extends ProxyComponent
{
    /**
     * Inputs.
     *
     * @var array
     */
    protected array $proxies = [];

    /**
     * Input callback.
     *
     * @var Callback|null
     */
    public ?Callback $inputCallback = null;

    /**
     * @inheritDoc
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->proxies = config('livewire-ext.inputs');
    }

    /**
     * Mount the component.
     *
     * @param string    $input      input name
     * @param mixed     $value      input value
     * @param array     $options    input options
     * @return void
     */
    public function mount(string $input, mixed $value = null, array $options = []): void
    {
        if ($value !== null) {
            $options['value'] = $value;
        }

        $this->changeProxy($input, $options);
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire-ext::widgets.smart-input');
    }

    /**
     * @inheritDoc
     */
    public function updated(string $key, mixed $value): void
    {
        if ($key == 'proxyData.value') {
            // Make sure the value is updated.
            $this->proxyService->setValue('value', $value);
            $this->fireChange();
        }
    }

    /**
     * Fires input change event.
     *
     * @return void
     */
    public function fireChange(): void
    {
        $values = $this->callProxyMethod('values');

        if ($this->inputCallback !== null) {
            $this->inputCallback->fire(...$values);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'widgets-smart-input';        
    }
}
