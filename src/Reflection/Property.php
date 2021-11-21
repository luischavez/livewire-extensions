<?php

namespace Luischavez\Livewire\Extensions\Reflection;

use ReflectionProperty;

/**
 * Reflection property.
 */
class Property
{
    /**
     * Searched object instance.
     *
     * 
     */
    protected $instance;

    /**
     * Name.
     *
     * @var string
     */
    protected string $name;

    /**
     * Type.
     *
     * @var string|null
     */
    protected ?string $type;

    /**
     * Value.
     *
     * 
     */
    protected $value;

    /**
     * Reflection property.
     *
     * @var ReflectionProperty
     */
    protected ReflectionProperty $reflectionProperty;

    /**
     * Constructor.
     *
     * @param                 $instance           searched object instance
     * @param ReflectionProperty    $reflectionProperty reflection property
     */
    public function __construct($instance, ReflectionProperty $reflectionProperty)
    {
        $this->instance = $instance;
        $this->name = $reflectionProperty->getName();
        $this->type = $reflectionProperty->getType() ? $reflectionProperty->getType()->getName() : null;

        if (!$reflectionProperty->isPublic()) {
            $reflectionProperty->setAccessible(true);
        }
        $this->value = $reflectionProperty->isInitialized($this->instance)
            ? $reflectionProperty->getValue($this->instance)
            : (
                $reflectionProperty->hasDefaultValue()
                    ? $reflectionProperty->getDefaultValue()
                    : null
            );

        $this->reflectionProperty = $reflectionProperty;
    }

    /**
     * Gets the property name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Gets the property type.
     *
     * @return string|null
     */
    public function type(): ?string
    {
        return $this->type;
    }

    /**
     * Gets the property value.
     *
     * 
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Check if the property is sublcass of type.
     *
     * @param string|null $type type to check
     * @return boolean
     */
    public function is(?string $type): bool
    {
        if ($this->type === null && $type === null) {
            return true;
        }

        return is_subclass_of($this->type, $type);
    }

    /**
     * Sets the value.
     *
     * @param $value value
     * @return void
     */
    public function set($value): void
    {
        $this->reflectionProperty->setAccessible(true);
        $this->reflectionProperty->setValue($this->instance, $value);
        $this->value = $value;
    }
}
