<?php

namespace Luischavez\Livewire\Extensions\Services;

use Luischavez\Livewire\Extensions\Exceptions\PropertyProtectionException;
use Luischavez\Livewire\Extensions\Reflection\Inspector;
use Luischavez\Livewire\Extensions\Reflection\InspectorQuery;
use Luischavez\Livewire\Extensions\Reflection\Method;
use Luischavez\Livewire\Extensions\Reflection\Property;

/**
 * Porperty protection service.
 */
class PropertyProtectionService extends LivewireService
{
    /**
     * Protected properties.
     *
     * @var array
     */
    protected array $protected = [];

    /**
     * Search all protected properties.
     *
     * @return void
     */
    protected function lookup(): void
    {
        $protected = [];
        $except = [];

        /**
         * @var Property|null
         */
        $protectedProperties = Inspector::inspect($this->component)
            ->property()
            ->withName('protectedProperties')
            ->withModifiers(InspectorQuery::PROTECTED_MODIFIER)
            ->first();

        if ($protectedProperties !== null) {
            /**
             * @var array
             */
            $protectedProperties = $protectedProperties->value();

            /**
             * @var Property
             */
            foreach ($protectedProperties as $name => $value) {
                if ($name == '_except') {
                    $except = $value;
                    continue;
                }

                $protected[] = $value;
            }
        }

        foreach (class_uses_recursive($this->component) as $trait) {
            $traitProtectPropertiesMethod = 'protectProperties'.class_basename($trait);

            /**
             * @var Method|null
             */
            $protectPropertiesMethod = Inspector::inspect($this->component)
                ->method()
                ->withName($traitProtectPropertiesMethod)
                ->withModifiers(InspectorQuery::PROTECTED_MODIFIER)
                ->first();

            if ($protectPropertiesMethod === null) {
                continue;
            }

            $traitProtectedProperties = $protectPropertiesMethod->invoke();

            $except = array_merge($except, $traitProtectedProperties['_except'] ?? []);
            $protected = array_merge($protected, $traitProtectedProperties ?? []);
        }

        $protected['_except'] = $except;

        $this->protected = $protected;
    }

    /**
     * @inheritDoc
     */
    public function updating(string $name, mixed $value): void
    {
        $this->lookup();
        self::throwIfProtected($name, $this->protected);
    }

    /**
     * Check if the property is protected.
     *
     * @param string    $name   field name
     * @param array     $fields fields to lookup
     * @return bool
     */
    private static function match(string $name, array $fields): bool
    {
        if (in_array($name, $fields)) return true;

        $nameSegments = explode('.', $name);

        foreach ($fields as $property) {
            if (!is_string($property)) continue;

            $protectedSegments = explode('.', $property);

            $matches = true;
            foreach ($protectedSegments as $protectedIndex => $protectedSegment) {
                if (!isset($nameSegments[$protectedIndex])) {
                    $matches = false;
                    break;
                }

                $nameSegment = $nameSegments[$protectedIndex];

                if ($protectedSegment == '*') {
                    $matches = true;
                    break;
                }

                if ($protectedSegment != $nameSegment) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                return true;
            }
        }

        return false;
    }

    /**
     * Throw an exception if the property is protected.
     *
     * @param string    $name                   name of the property
     * @param array     $protectedProperties    protected properties
     * @return void
     * 
     * @throws PropertyProtectionException
     */
    public static function throwIfProtected(string $name, array $protectedProperties): void
    {
        $except = $protectedProperties['_except'] ?? [];
        
        if (!self::match($name, $except)
            && self::match($name, $protectedProperties)) {
            throw new PropertyProtectionException("Property $name is protected");
        }
    }
}
