<?php

namespace Luischavez\Livewire\Extensions;

use Livewire\Component;
use Luischavez\Livewire\Extensions\Exceptions\CallbackException;
use Luischavez\Livewire\Extensions\Services\TaggingService;
use Luischavez\Livewire\Extensions\Utils\RouteUtils;
use Luischavez\Livewire\Extensions\Utils\SecurityUtils;

/**
 * Callback.
 */
class Callback extends Transformable
{
    /**
     * Enables encryption.
     *
     * @var boolean
     */
    protected static bool $encrypted = true;

    /**
     * Component.
     *
     * @var Component
     */
    protected Component $component;

    /**
     * Caller of this callback.
     *
     * @var array
     */
    protected array $caller = [
        'type' => null,
        'name' => null,
    ];

    /**
     * Component that creates this callback.
     *
     * @var array
     */
    protected array $creator = [
        'name'  => null,
        'id'    => null,
    ];

    /**
     * Extra data to return on fire.
     *
     * @var array
     */
    protected array $extra = [];

    /**
     * Routes to execute on fire.
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * Constructor.
     *
     * @param $caller caller
     */
    public function __construct($caller = null)
    {
        if ($caller !== null) {
            if ($caller instanceof Component) {
                $this->caller['type'] = 'component';
                $this->caller['name'] = $caller->getName();
                $this->component = $caller;
            } else if ($caller instanceof Caller) {
                $this->caller['type'] = $caller->type();
                $this->caller['name'] = $caller->name();
                $this->component = $caller->component();
            }

            if ($this->component !== null) {
                $this->creator['name'] = $this->component->getName();
                $this->creator['id'] = $this->component->id;
            }
        }
    }

    /**
     * Sets the component.
     *
     * @param Component $component component
     * @return void
     */
    public function setComponent(Component $component): void
    {
        $this->component = $component;
    }

    /**
     * Add a route.
     *
     * @param string        $type           type
     * @param string        $event          event
     * @param string|null   $tag            tag
     * @param string|null   $component      component
     * @param          ...$parameters  parameters
     * @return void
     */
    protected function addRoute(string $type, string $event, ?string $tag = null, ?string $component = null, ...$parameters): void
    {
        $this->routes[] = [
            'type'          => $type,
            'route'         => RouteUtils::toRoute($event, $tag, $component),
            'parameters'    => $parameters,
        ];
    }

    /**
     * Gets the declared routes.
     *
     * @return array
     */
    public function routes(): array
    {
        return $this->routes;
    }

    /**
     * Add an extra data to return on callback fire.
     *
     * @param ...$value
     * @return self
     */
    public function with(...$value): self
    {
        $this->extra = array_merge($this->extra, $value);

        return $this;
    }

    /**
     * Add a route.
     *
     * @param string        $event
     * @param string|null   $tag
     * @param string|null   $component
     * @param         ...$parameters
     * @return self
     */
    public function to(string $event, ?string $tag = null, ?string $component = null, ...$parameters): self
    {
        $this->addRoute('component', $event, $tag, $component, ...$parameters);

        return $this;
    }

    /**
     * Add a self route.
     *
     * @param string    $event
     * @param     ...$parameters
     * @return self
     */
    public function toSelf(string $event, ...$parameters): self
    {
        if (!$this->caller) {
            throw new CallbackException("Cant resolve caller for this callback");
        }

        $type = $this->caller['type'];

        /**
         * @var TaggingService
         */
        $taggingService = TaggingService::of($this->component);

        $tag = $taggingService !== null
            ? $taggingService->tag()
            : null;

        if ($type != 'component') {
            $parameters = [
                $type,
                $this->caller['name'],
                $event,
                $parameters,
            ];

            $event = 'callerCallback';
        }

        $this->addRoute($type, $event, $tag, $this->component->getName(), ...$parameters);

        return $this;
    }

    /**
     * Fires this callback.
     *
     * @param ...$parameters additional parameters
     * @return void
     */
    public function fire(...$parameters)
    {
        foreach ($this->routes() as $definition) {
            $route = $definition['route'];

            if ($definition['type'] != 'component') {
                $definition['parameters'][3] = array_merge($this->extra, $definition['parameters'][3], $parameters);
                $routeParameters = $definition['parameters'];
            } else {
                $routeParameters = array_merge($this->extra, $definition['parameters'], $parameters);
            }

            TaggingService::emitToRouteWithComponent($this->component, $route, ...$routeParameters);
        }
    }

    /**
     * @inheritDoc
     */
    public function toJavascript()
    {
        $data = [
            'caller'    => $this->caller,
            'creator'   => $this->creator,
            'extra'     => $this->extra,
            'routes'    => $this->routes,
        ];

        SecurityUtils::throwIfInvalid($data);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function fromJavascript($value): Callback
    {
        if (!is_array($value)) {
            return null;
        }

        $caller = $value['caller'] ?? [];
        $creator = $value['creator'] ?? [];
        $extra = $value['extra'] ?? [];
        $routes = $value['routes'] ?? [];

        $callback = new Callback();
        $callback->caller = $caller;
        $callback->creator = $creator;
        $callback->extra = $extra;
        $callback->routes = $routes;

        return $callback;
    }
}
