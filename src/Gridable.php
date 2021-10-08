<?php

namespace Luischavez\Livewire\Extensions;

use Illuminate\Support\Str;

/**
 * Gridable.
 */
abstract class Gridable
{
    /**
     * Gets the items.
     *
     * @return array
     */
    public abstract function items(): array;

    /**
     * Gets the component name.
     *
     * @return string
     */
    public abstract function component(): string;

    /**
     * Gets the primary filter parameters.
     *
     * @return array
     */
    protected abstract function filterParameters(): array;

    /**
     * Apply a filter.
     *
     * @param string    $name   filter name
     * @param mixed     $value  value
     * @return void
     */
    public function applyFilter(string $name, mixed $value): void
    {
        $name = Str::studly($name);
        $filterMethodName = "filterBy$name";

        if (method_exists($this, $filterMethodName)) {
            $this->{$filterMethodName}(...$this->filterParameters(), $value);
        }
    }
}
