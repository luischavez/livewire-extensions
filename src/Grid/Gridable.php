<?php

namespace Luischavez\Livewire\Extensions\Grid;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Exceptions\ComponentNotFoundException;
use Livewire\Livewire;

/**
 * Gridable.
 */
abstract class Gridable
{
    /**
     * Gets the grid data.
     *
     * @param bool  $paginate   paginate results
     * @param int   $perPage    items per page
     * @param int   $page       current page
     * @return GridData
     */
    public abstract function data(bool $paginate = true, int $perPage = 10, int $page = 1): GridData;

    /**
     * Render the grid.
     * You can return a view, string or a livewire component name.
     *
     * @param mixed $item item
     * @return mixed
     */
    public abstract function render(mixed $item): mixed;

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

    /**
     * Gets the item key.
     *
     * @param mixed $item item
     * @return mixed
     */
    public function key(mixed $item): mixed
    {
        return null;
    }

    /**
     * Transform the item before send to view.
     *
     * @param mixed $item item
     * @return mixed
     */
    public function transform(mixed $item): mixed
    {
        return $item;
    }

    /**
     * Gets the item name.
     *
     * @return string
     */
    public function itemName(): string
    {
        return 'item';
    }

    /**
     * Gets the view properties.
     *
     * @param mixed $item item
     * @return array
     */
    public function properties(mixed $item): array
    {
        return [];
    }

    /**
     * Output the grid item.
     *
     * @param mixed $item item
     * @return string
     */
    public function output(mixed $item): string
    {
        $key = $this->key($item);

        $item = $this->transform($item);
        $content = $this->render($key, $item);

        if ($content instanceof View) {
            $content = $content
                ->with($this->properties($item))
                ->with('key', $key)
                ->with($this->itemName(), $item)
                ->render();
        } else {
            $content = strval($content);

            try {
                Livewire::getClass($content);

                $properties = $this->properties($item);
                $properties[$this->itemName()] = $item;

                $content = view('livewire-ext::widgets.spawn', [
                    'component'             => $content,
                    'key'                   => $key,
                    'componentProperties'   => $properties,
                ])->render();
            } catch (ComponentNotFoundException $ex) {}
        }

        return $content;
    }
}
