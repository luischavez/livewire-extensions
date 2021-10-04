<?php

namespace Luischavez\Livewire\Extensions;

use Livewire\LivewireServiceProvider;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\Method;
use Luischavez\Livewire\Extensions\Reflection\Property;

/**
 * Temp fix for octane kept the listeners on memory on each request.
 */
class OctaneLivewireFixer
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event): void
    {
        if (! $event->sandbox->resolved('livewire')) {
            return;
        }

        /**
         * @var Property|null
         */
        $listeners = Inspector::inspect($event->sandbox['livewire'])
            ->property()
            ->withName('listeners')
            ->first();

        if ($listeners !== null) {
            $listeners->set([]);

            $livewireServiceProvider = new LivewireServiceProvider($event->sandbox);

            /**
             * @var Method|null
             */
            $registerFeatures = Inspector::inspect($livewireServiceProvider)
                ->method()
                ->withName('registerFeatures')
                ->first();

            if ($registerFeatures !== null) {
                $registerFeatures->invoke();
            }
        }
    }
}
