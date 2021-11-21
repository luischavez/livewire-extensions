<?php

use Luischavez\Livewire\Extensions\Callback;

if (!function_exists('callback')) {
    /**
     * Create a callback
     *
     * @param $caller caller
     * @return Callback
     */
    function callback($caller = null): Callback {
        return new Callback($caller);
    }
}
