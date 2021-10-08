<?php

namespace Luischavez\Livewire\Extensions\Utils;

use Luischavez\Livewire\Extensions\Exceptions\RouteException;

/**
 * Route utils.
 */
class RouteUtils
{
    /**
     * Build a component event route string.
     *
     * @param string            $event
     * @param string|null       $tag
     * @param string|null       $component
     * @return string
     */
    public static function toRoute(string $event, ?string $tag = null, ?string $component = null): string
    {
        $component = $component ?? '';
        $tag = $tag ?? '';

        return "$component::$tag@$event";
    }

    /**
     * Parses the string route to an array with the data.
     *
     * @param string $route
     * @return array
     * 
     * @throws InvalidRouteException
     */
    public static function fromRouteString(string $route): array
    {
        $route = trim($route);
        $routeData = explode('@', $route, 2);
        $event = count($routeData) == 1 ? $routeData[0] : $routeData[1];
        $target = count($routeData) == 2 ? explode('::', $routeData[0], 2) : [];
        $component = count($target) >= 1 ? $target[0] : null;
        $tag = count($target) > 1 ? $target[1] : null;

        if (empty($component)) $component = null;
        if (empty($tag)) $tag = null;
        if (empty($event)) $event = null;

        if ($event === null) {
            throw new RouteException("Invalid route: $route");
        }

        if ($tag !== null) {
            $event .= "::$tag";
        }

        return [
            'component' => $component,
            'event'     => $event,
        ];
    }
}
