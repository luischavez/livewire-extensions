<?php

use Livewire\Component;
use Luischavez\Livewire\Extensions\Callback;
use Luischavez\Livewire\Extensions\Caller;

if (!function_exists('callback')) {
    /**
     * Create a callback
     *
     * @param Component|Caller|null $caller caller
     * @return Callback
     */
    function callback(Component|Caller|null $caller = null): Callback {
        return new Callback($caller);
    }
}
