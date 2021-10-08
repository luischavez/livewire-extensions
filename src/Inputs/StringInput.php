<?php

namespace Luischavez\Livewire\Extensions\Inputs;

use Illuminate\Contracts\View\View;

/**
 * String input.
 */
class StringInput extends Input
{
    /**
     * Input placeholder.
     * 
     * @var string|null
     */
    public ?string $placeholder = null;

    /**
     * Flag to specify if the input will be hidden.
     * 
     * @var bool
     */
    public bool $password = false;

    /**
     * Protected properties.
     *
     * @var array
     */
    protected array $protectedProperties = [
        'placeholder',
        'password',
    ];

    /**
     * @inheritDoc
     */
    public function mount(): void
    {
        $this->inputRules['type'] = 'string';
    }

    /**
     * @inheritDoc
     */
    public function render(): ?View
    {
        return view('livewire-ext::inputs.string');
    }
}
