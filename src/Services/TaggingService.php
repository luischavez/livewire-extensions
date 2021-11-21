<?php

namespace Luischavez\Livewire\Extensions\Services;

use Illuminate\Support\Str;
use Livewire\Component;
use Luischavez\Livewire\Extensions\Utils\RouteUtils;

/**
 * Tagging service.
 */
class TaggingService extends LivewireService
{
    /**
     * @inheritDoc
     */
    public function mount(): void
    {
        if (empty($this->component->tag)) {
            $this->component->tag = static::generateTag();
        }
    }

    /**
     * Gets the component tag.
     *
     * @return string
     */
    public function tag(): string
    {
        return $this->component->getTag() ?? '';
    }

    /**
     * Emit an event.
     *
     * @param string        $event
     * @param string|null   $tag
     * @param string|null   $component
     * @param mixed         ...$parameters
     * @return void
     */
    public function emitEvent(string $event, ?string $tag = null, ?string $component = null, mixed ...$parameters): void
    {
        $route = RouteUtils::fromRouteString(RouteUtils::toRoute($event, $tag, $component));

        $component = $route['component'];
        $event = $route['event'];

        if (empty($component)) {
            $this->component->emit($event, ...$parameters);
        } else {
            $this->component->emitTo($component, $event, ...$parameters);
        }
    }

    /**
     * Emit an event to a route.
     *
     * @param string    $routeString    route string
     * @param mixed     ...$parameters  parameters
     * @return void
     */
    public function emitToRoute(string $routeString, mixed ...$parameters): void
    {
        self::emitToRouteWithComponent($this->component, $routeString, ...$parameters);  
    }

    /**
     * Generate a new tag.
     *
     * @return string
     */
    public static function generateTag(): string
    {
        return Str::random(16);
    }

    /**
     * Emit an event to a route from a specific component.
     *
     * @param Component $component      component
     * @param string    $routeString    route string
     * @param mixed     ...$parameters  parameters
     * @return void
     */
    public static function emitToRouteWithComponent(Component $component, string $routeString, mixed ...$parameters): void
    {
        $route = RouteUtils::fromRouteString($routeString);

        $componentName = $route['component'];
        $event = $route['event'];

        if (empty($componentName)) {
            $component->emit($event, ...$parameters);
        } else {
            $component->emitTo($componentName, $event, ...$parameters);
        }
    }
}
