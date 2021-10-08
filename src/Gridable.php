<?php

namespace Luischavez\Livewire\Extensions;

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
     * Gets the items.
     *
     * @param int $page     current page
     * @param int $perPage  items per page
     * @return array
     */
    public abstract function items(int $page = 1, int $perPage = 10): array;

    /**
     * Render the grid.
     * You can return a view, string or a livewire component name.
     *
     * @param mixed $key    key
     * @param mixed $item   item
     * @return mixed
     */
    public abstract function render(mixed $key, mixed $item): mixed;

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
     * Output the grid item.
     *
     * @param mixed $key    key
     * @param mixed $item   item
     * @return string
     */
    public function output(mixed $key, mixed $item): string
    {
        $item = $this->transform($item);
        $content = $this->render($key, $item);

        if ($content instanceof View) {
            $content = $content->with('item', $item)->render();
        } else {
            $content = strval($content);

            try {
                Livewire::getClass($content);

                $content = view('livewire-ext::widgets.spawn', [
                    'component'             => $content,
                    'componentProperties'   => [
                        'key'   => $key,
                        'item'  => $item,
                    ],
                ])->render();
            } catch (ComponentNotFoundException $ex) {}
        }

        return $content;
    }
}
