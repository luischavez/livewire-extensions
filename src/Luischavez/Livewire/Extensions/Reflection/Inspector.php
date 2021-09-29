<?php

namespace Luischavez\Livewire\Extensions\Reflection;

/**
 * Class inspector.
 */
class Inspector
{
    /**
     * Instance to inspect.
     *
     * @var mixed
     */
    protected mixed $instance;

    protected function __construct(mixed $instance)
    {
        $this->instance = $instance;
    }

    /**
     * Query propertyies.
     *
     * @return InspectorQuery
     */
    public function property(): InspectorQuery
    {
        return new InspectorQuery($this->instance, InspectorQuery::PROPERTY);
    }

    /**
     * Query methods.
     *
     * @return InspectorQuery
     */
    public function method(): InspectorQuery
    {
        return new InspectorQuery($this->instance, InspectorQuery::METHOD);
    }

    /**
     * Creates a new inspector.
     *
     * @param mixed $instance instance to inspect
     * @return self
     */
    public static function inspect(mixed $instance): self
    {
        return new Inspector($instance);
    }
}
