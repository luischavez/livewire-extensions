<?php

namespace Luischavez\Livewire\Extensions\Reflection;

use ReflectionMethod;

/**
 * Reflection method.
 */
class Method
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
     * Return type.
     *
     * @var string|null
     */
    protected ?string $returnType;

    /**
     * Reflection method object.
     *
     * @var ReflectionMethod
     */
    protected ReflectionMethod $reflectionMethod;

    /**
     * Constructor.
     *
     * @param mixed             $instance   searched object instance
     * @param ReflectionMethod  $object     reflection instance
     */
    public function __construct(mixed $instance, ReflectionMethod $reflectionMethod)
    {
        $this->instance = $instance;
        $this->name = $reflectionMethod->getName();
        $this->returnType = $reflectionMethod->getReturnType();
        $this->reflectionMethod = $reflectionMethod;
    }

    /**
     * Name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Return type.
     *
     * @return string|null
     */
    public function returnType(): ?string
    {
        return $this->returnType;
    }

    /**
     * Invoke the method and gets the result.
     *
     * @param mixed ...$parameters parameters
     * @return mixed
     */
    public function invoke(mixed ...$parameters): mixed
    {
        if (!$this->reflectionMethod->isPublic()) {
            $this->reflectionMethod->setAccessible(true);
        }
        return $this->reflectionMethod->invoke($this->instance, ...$parameters);
    }
}
