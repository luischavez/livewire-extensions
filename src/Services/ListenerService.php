<?php

namespace Luischavez\Livewire\Extensions\Services;

use Illuminate\Support\Str;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\InspectorQuery;
use Luischavez\Livewire\Extensions\Reflection\Method;

/**
 * Listener service.
 */
class ListenerService extends LivewireService
{
    /**
     * Listener methods.
     *
     * @var array
     */
    protected array $listenerMethods = [];

    /**
     * Search the declared listeners.
     *
     * @return void
     */
    protected function lookup(): void
    {
        $methods = Inspector::inspect($this->component)
            ->method()
            ->withName('/^on[A-Z].+/')
            ->withModifiers(InspectorQuery::PUBLIC_MODIFIER)
            ->all();

        $this->listenerMethods = [];
        /**
         * @var Method
         */
        foreach ($methods as $method) {
            $this->listenerMethods[] = $method->name();
        }
    }

    /**
     * Merge and get the listeners.
     *
     * @param array $definedListeners component defined listeners
     * @return array
     */
    public function merge(array $definedListeners = []): array
    {
        /**
         * @var TaggingService
         */
        $taggingService = TaggingService::of($this->component);

        $tag = $taggingService->tag();

        $listeners = [];
        foreach ($this->listenerMethods as $method) {
            $listener = preg_replace('/^(on)/', '', $method);
            $listener = Str::camel($listener);

            $listeners[$listener] = $method;

            if (!empty($tag)) {
                $listener .= "::{$tag}";
                $listeners[$listener] = $method;
            }
        }

        return array_merge($definedListeners, $listeners);
    }

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->lookup();
    }
}
