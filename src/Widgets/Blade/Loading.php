<?php

namespace Luischavez\Livewire\Extensions\Widgets\Blade;

use Illuminate\View\Component;

/**
 * Loading for livewire components.
 */
class Loading extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        return view('livewire-ext::widgets.blade.loading');
    }
}
