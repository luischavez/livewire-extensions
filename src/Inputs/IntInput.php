<?php

namespace Luischavez\Livewire\Extensions\Inputs;

use Illuminate\Contracts\View\View;

/**
 * Int input model.
 */
class IntInput extends Input
{
    /**
     * @inheritDoc
     */
    public function mount(): void
    {
        $this->rules[] = 'numeric';
    }

    /**
     * @inheritDoc
     */
    public function render(): ?View
    {
        return view('livewire-ext::inputs.int');
    }
}
