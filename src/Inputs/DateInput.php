<?php

namespace Luischavez\Livewire\Extensions\Inputs;

use Illuminate\Contracts\View\View;

/**
 * Date input model.
 */
class DateInput extends Input
{
    /**
     * @inheritDoc
     */
    public function mount(): void
    {
        $this->inputRules['type'] = 'date';
    }

    /**
     * @inheritDoc
     */
    public function render(): ?View
    {
        return view('livewire-ext::inputs.date');
    }
}
