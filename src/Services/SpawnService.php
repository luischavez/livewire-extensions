<?php

namespace Luischavez\Livewire\Extensions\Services;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\LifecycleManager;
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
     * @param string $spawner    spawner name
     * @param  $component  component name or instance
     * @param array  $properties component properties
     * @return void
     */
    public function spawn(string $spawner, $component, array $properties = []): void
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

        $lifecycleManager = LifecycleManager::fromInitialRequest($componentName, Str::random(20));

        $response = $lifecycleManager
            ->initialHydrate()
            ->mount($properties)
            ->renderToView()
            ->initialDehydrate()
            ->toInitialResponse();

        $view = $response->html();

        /**
         * @var Property|null
         */
        $spawnedEventQueue = Inspector::inspect($lifecycleManager->instance)
            ->property()
            ->withName('eventQueue')
            ->first();

        /**
         * @var Property|null
         */
        $spawnedDispatchQueue = Inspector::inspect($lifecycleManager->instance)
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

        $this->component->dispatchBrowserEvent("spawn-$spawner", compact('view'));
    }
}
