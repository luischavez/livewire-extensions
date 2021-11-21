<?php

namespace Luischavez\Livewire\Extensions\Reflection;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Reflection inspector query.
 */
class InspectorQuery
{
    const PROPERTY  = 'property';
    const METHOD    = 'method';

    const PUBLIC_MODIFIER       = 'public';
    const PROTECTED_MODIFIER    = 'protected';
    const PRIVATE_MODIFIER      = 'private';
    const STATIC_MODIFIER       = 'static';

    /**
     * Instance of the object to be searched.
     *
     * 
     */
    protected $instance;

    /**
     * Target of this query.
     *
     * @var string
     */
    protected string $target;

    /**
     * Name of the requested object.
     *
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * Type to search.
     *
     * @var string|null
     */
    protected ?string $type = null;

    /**
     * Modifiers to search.
     *
     * @var array
     */
    protected array $modifiers = [];

    /**
     * Constructor.
     *
     * @param     $instance  object instance
     * @param string    $target    target
     */
    public function __construct($instance, string $target)
    {
        $this->instance = $instance;
        $this->target = $target;
    }

    /**
     * Calculates the modifiers flag.
     *
     * @return integer|null
     */
    protected function calculateModifiersFlag(): ?int
    {
        $modifiers = [];

        foreach ($this->modifiers as $modifier) {
            switch ($modifier) {
                case self::PUBLIC_MODIFIER:
                    $modifiers[] = $this->target === self::PROPERTY
                        ? ReflectionProperty::IS_PUBLIC
                        : ReflectionMethod::IS_PUBLIC;
                    break;
                case self::PROTECTED_MODIFIER:
                    $modifiers[] = $this->target === self::PROPERTY
                        ? ReflectionProperty::IS_PROTECTED
                        : ReflectionMethod::IS_PROTECTED;
                    break;
                case self::PRIVATE_MODIFIER:
                    $modifiers[] = $this->target === self::PROPERTY
                        ? ReflectionProperty::IS_PRIVATE
                        : ReflectionMethod::IS_PRIVATE;
                    break;
                case self::STATIC_MODIFIER:
                    $modifiers[] = $this->target === self::PROPERTY
                        ? ReflectionProperty::IS_STATIC
                        : ReflectionMethod::IS_STATIC;
                    break;
            }
        }

        if (empty($modifiers)) {
            return null;
        }

        $flags = 0;

        foreach ($modifiers as $modifier) {
            $flags |= $modifier;
        }

        return $flags;
    }

    /**
     * Filter by name or name pattern.
     *
     * @param string $name name of the property or method
     * @return self
     */
    public function withName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Filter by type.
     *
     * @param string $type type of the property or method.
     * @return self
     */
    public function withType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Filter by modifiers.
     *
     * @param string ...$modifiers modifiers
     * @return self
     */
    public function withModifiers(string ...$modifiers): self
    {
        $this->modifiers = $modifiers;
        return $this;
    }

    /**
     * Get all the results.
     *
     * @param integer $limit limit the results
     * @return array
     */
    public function all(int $limit = 0): array
    {
        $reflectionClass = new ReflectionClass($this->instance);

        $modifiersFlag = $this->calculateModifiersFlag();

        $objects = $this->target === self::PROPERTY
            ? $reflectionClass->getProperties($modifiersFlag)
            : $reflectionClass->getMethods($modifiersFlag);

        $results = [];

        /**
         * @var ReflectionProperty|ReflectionMethod
         */
        foreach ($objects as $object) {
            $type = $this->target === self::PROPERTY
                ? $object->getType()
                : $object->getReturnType();

            if ($this->type !== null) {
                $typeName = $type?->getName();

                if ($typeName !== $this->type
                    && !is_subclass_of($typeName, $this->type)) {
                    continue;
                }
            }

            if ($this->name !== null) {
                $name = $object->getName();
            
                if (str_starts_with($this->name, '/') && str_ends_with($this->name, '/')) {
                    if (!preg_match_all($this->name, $name, $matches)) {
                        continue;
                    }
                } else {
                    if ($name != $this->name) {
                        continue;
                    }
                }
            }

            $results[] = $this->target === self::PROPERTY
                ? new Property($this->instance, $object)
                : new Method($this->instance, $object);

            if ($limit > 0 && count($results) == $limit) {
                break;
            }
        }

        return $results;
    }

    /**
     * Gets the first result.
     *
     * 
     */
    public function first()
    {
        $results = $this->all(1);

        return count($results) ? $results[0] : null;
    }
}
