<?php

namespace Luischavez\Livewire\Extensions\Inputs;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

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
        $this->rules[] = 'date';

        if ($this->value) {
            $date = Carbon::createFromTimeString($this->value);
            $this->value = $date->format('Y-m-d');
        }
    }

    /**
     * @inheritDoc
     */
    public function render(): ?View
    {
        return view('livewire-ext::inputs.date');
    }
}
