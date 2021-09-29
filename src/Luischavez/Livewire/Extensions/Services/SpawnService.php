<?php

namespace Luischavez\Livewire\Extensions\Services;

use Livewire\Component;
use Luischavez\Livewire\Extensions\ExtendedComponent;
use Luischavez\Livewire\Extensions\LivewireExtensionsManager;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\InspectorQuery;
use Luischavez\Livewire\Extensions\Reflection\Property;

/**
 * Spawn service.
 */
class SpawnService extends LivewireService
{
    /**
     * Spawn a component.
     *
     * @param string                    $spawner    spawner name
     * @param ExtendedComponent|string  $component  component name or instance
     * @param array                     $properties component properties
     * @return void
     */
    public function spawn(string $spawner, ExtendedComponent|string $component, array $properties = []): void
    {
        $componentName = $component;

        $tag = $properties['tag'] ?? TaggingService::generateTag();

        if ($component instanceof Component) {
            $tag = $component->tag ?? $tag;

            $componentName = $component->getName();

            $alreadyDefinedProperties = Inspector::inspect($component)
                ->property()
                ->withModifiers(InspectorQuery::PUBLIC_MODIFIER)
                ->all();

            $alreadyDefinedProperties = collect($alreadyDefinedProperties);
            $alreadyDefinedProperties = $alreadyDefinedProperties
                ->map(function($property) {
                    return [
                        'name'  => $property->name(),
                        'value' => $property->value(),
                    ];
                })
                ->keyBy('name')
                ->map(function($property) {
                    return $property['value'];
                })
                ->toArray();

            $properties = array_merge($alreadyDefinedProperties, $properties);
        }

        $properties['tag'] = $tag;

        $view = view('livewire-ext::widgets.spawn', [
            'component'             => $componentName,
            'componentProperties'   => $properties,
        ])->render();

        /**
         * @var LivewireExtensionsManager
         */
        $livewire = app('livewire');
        
        /**
         * @var ExtendedComponent|null
         */
        $component = null;

        foreach ($livewire->instances() as $instance) {
            if ($instance->tag == $tag) {
                $component = $instance;
                //dd($component);
            }
        }

        if ($component !== null) {
            /**
             * @var Property|null
             */
            $spawnedEventQueue = Inspector::inspect($component)
                ->property()
                ->withName('eventQueue')
                ->first();
            /**
             * @var Property|null
             */
            $spawnedDispatchQueue = Inspector::inspect($component)
                ->property()
                ->withName('dispatchQueue')
                ->first();
            
            if (!empty($spawnedEventQueue->value())) {
                /**
                 * @var Property|null
                 */
                $eventQueue = Inspector::inspect($this->component)
                    ->property()
                    ->withName('eventQueue')
                    ->first();

                /**
                 * @var array
                 */
                $events = $eventQueue->value();
                $events = array_merge($events, $spawnedEventQueue->value());
                $eventQueue->set($events);
            }

            if (!empty($spawnedDispatchQueue->value())) {
                /**
                 * @var Property|null
                 */
                $dispatchQueue = Inspector::inspect($this->component)
                    ->property()
                    ->withName('dispatchQueue')
                    ->first();
                
                /**
                 * @var array
                 */
                $dispatched = $dispatchQueue->value();
                $dispatched = array_merge($dispatched, $spawnedDispatchQueue->value());
                $dispatchQueue->set($dispatched);
            }    
        }

        $this->component->dispatchBrowserEvent("spawn-$spawner", compact('view'));
    }
}
