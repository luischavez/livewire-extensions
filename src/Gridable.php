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
     * Apply a filters.
     *
     * @param array $filter filters
     * @return void
     */
    public function applyFilters(array $filters): void
    {
        foreach ($filters as $filter => $value) {
            $filter = Str::studly($filter);
            $filterMethodName = "filterBy$filter";

            if (method_exists($this, $filterMethodName)) {
                $this->{$filterMethodName}($value, ...$this->filterParameters());
            }
        }
    }
}
