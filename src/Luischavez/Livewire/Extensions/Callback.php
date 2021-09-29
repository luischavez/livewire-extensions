<?php

namespace Luischavez\Livewire\Extensions;

use Livewire\Component;
use Livewire\LivewireManager;
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
    protected array $component = [
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
     * @param Component|Caller|null $caller caller
     */
    public function __construct(Component|Caller|null $caller = null)
    {
        if ($caller !== null) {
            $component = null;
            if ($caller instanceof Component) {
                $this->caller['type'] = 'component';
                $this->caller['name'] = $caller->getName();
                $component = $caller;
            } else if ($caller instanceof Caller) {
                $this->caller['type'] = $caller->type();
                $this->caller['name'] = $caller->name();
                $component = $caller->component();
            }

            if ($component !== null) {
                $this->component['name'] = $component->getName();
                $this->component['id'] = $component->id;
            }
        }
    }

    /**
     * Gets the component.
     *
     * @return Component|null
     */
    protected function component(): ?Component
    {
        /**
         * @var LivewireExtensionsManager
         */
        $livewire = app()->make(LivewireManager::class);
        $component = $livewire->findInstance($this->component['id']);

        if ($component === null) {
            $component = $livewire->firstComponent();
        }

        return $component;
    }

    /**
     * Add a route.
     *
     * @param string        $type           type
     * @param string        $event          event
     * @param string|null   $tag            tag
     * @param string|null   $component      component
     * @param mixed         ...$parameters  parameters
     * @return void
     */
    protected function addRoute(string $type, string $event, ?string $tag = null, ?string $component = null, mixed ...$parameters): void
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
     * @param mixed ...$value
     * @return self
     */
    public function with(mixed ...$value): self
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
     * @param mixed         ...$parameters
     * @return self
     */
    public function to(string $event, ?string $tag = null, ?string $component = null, mixed ...$parameters): self
    {
        $this->addRoute('component', $event, $tag, $component, ...$parameters);

        return $this;
    }

    /**
     * Add a self route.
     *
     * @param string    $event
     * @param mixed     ...$parameters
     * @return self
     */
    public function toSelf(string $event, mixed ...$parameters): self
    {
        if (!$this->caller) {
            throw new CallbackException("Cant resolve caller for this callback");
        }

        $type = $this->caller['type'];
        $component = $this->component();

        /**
         * @var TaggingService
         */
        $taggingService = TaggingService::of($component);

        $tag = $taggingService !== null
            ? $taggingService->tag()
            : null;

        $parameters = array_merge($this->extra, $parameters);

        if ($type != 'component') {
            $parameters = [
                $type,
                $this->caller['name'],
                $event,
                $parameters,
            ];

            $event = 'callerCallback';
        }

        $this->addRoute($type, $event, $tag, $component->getName(), ...$parameters);

        return $this;
    }

    /**
     * Fires this callback.
     *
     * @param mixed ...$parameters additional parameters
     * @return void
     */
    public function fire(mixed ...$parameters)
    {
        $component = $this->component();

        foreach ($this->routes() as $definition) {
            $route = $definition['route'];

            if ($definition['type'] != 'component') {
                $definition['parameters'][3] = array_merge($this->extra, $definition['parameters'][3], $parameters);
                $routeParameters = $definition['parameters'];
            } else {
                $routeParameters = array_merge($this->extra, $definition['parameters'], $parameters);
            }

            TaggingService::emitToRouteWithComponent($component, $route, ...$routeParameters);
        }
    }

    /**
     * @inheritDoc
     */
    public function toJavascript(): mixed
    {
        $data = [
            'caller'    => $this->caller,
            'component' => $this->component,
            'extra'     => $this->extra,
            'routes'    => $this->routes,
        ];

        SecurityUtils::throwIfInvalid($data);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function fromJavascript(mixed $value): Callback
    {
        if (!is_array($value)) {
            return null;
        }

        $caller = $value['caller'] ?? [];
        $component = $value['component'] ?? [];
        $extra = $value['extra'] ?? [];
        $routes = $value['routes'] ?? [];

        $callback = new Callback();
        $callback->caller = $caller;
        $callback->component = $component;
        $callback->extra = $extra;
        $callback->routes = $routes;

        return $callback;
    }
}
