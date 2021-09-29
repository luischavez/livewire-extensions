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
     * @var mixed
     */
    protected mixed $instance;

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
     * @var mixed
     */
    protected mixed $value;

    /**
     * Reflection property.
     *
     * @var ReflectionProperty
     */
    protected ReflectionProperty $reflectionProperty;

    /**
     * Constructor.
     *
     * @param mixed                 $instance           searched object instance
     * @param ReflectionProperty    $reflectionProperty reflection property
     */
    public function __construct(mixed $instance, ReflectionProperty $reflectionProperty)
    {
        $this->instance = $instance;
        $this->name = $reflectionProperty->getName();
        $this->type = $reflectionProperty->getType()?->getName();

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
     * @return mixed
     */
    public function value(): mixed
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
     * @param mixed $value value
     * @return void
     */
    public function set(mixed $value): void
    {
        $this->reflectionProperty->setAccessible(true);
        $this->reflectionProperty->setValue($this->instance, $value);
        $this->value = $value;
    }
}
