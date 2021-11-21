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
     * Applied filters.
     *
     * @var array
     */
    protected array $filters = [];

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
     * @param $item item
     */
    public abstract function render($item);

    /**
     * Gets the item key.
     *
     * @param $item item
     */
    public function key($item)
    {
        return null;
    }

    /**
     * Gets the item name.
     *
     * @param $item item
     * @return string
     */
    public function itemName($item): string
    {
        return 'item';
    }

    /**
     * Gets the item properties.
     *
     * @param $item
     * @return array
     */
    public function properties($item): array
    {
        return [];
    }

    /**
     * Filter results.
     *
     * @param array $filters filters
     * @return void
     */
    public function filter(array $filters): void
    {
        foreach ($filters as $filter => $value) {
            $filter = Str::studly($filter);
            $filterMethodName = "filterBy$filter";

            if (method_exists($this, $filterMethodName)) {
                $this->{$filterMethodName}($value);
            }
        }
    }

    /**
     * Apply a filters.
     *
     * @param array $filter filters
     * @return void
     */
    public function applyFilters(array $filters): void
    {
        $this->filters = $filters;
        $this->filter($filters);
    }

    /**
     * Output the grid item.
     *
     * @param $item item
     * @return string
     */
    public function output($item): string
    {
        $key = $this->key($item);
        $name = $this->itemName($item);
        $properties = $this->properties($item);

        $properties[$name] = $item;

        $content = $this->render($item);

        if ($content instanceof View) {
            $content = $content
                ->with($properties)
                ->with('key', $key)
                ->render();
        } else {
            $content = strval($content);

            try {
                Livewire::getClass($content);

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
