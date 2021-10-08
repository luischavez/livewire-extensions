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
     * @param int $page     current page
     * @param int $perPage  items per page
     * @return array
     */
    public abstract function items(int $page = 1, int $perPage = 10): array;

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
