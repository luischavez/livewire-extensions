<?php

namespace Luischavez\Livewire\Extensions\Grid;

/**
 * Array gridable.
 */
abstract class ArrayGridable extends Gridable
{
    /**
     * Items.
     *
     * @var array
     */
    protected array $items = [];

    /**
     * @inheritDoc
     */
    public function data(bool $paginate = true, int $perPage = 10, int $page = 1): GridData
    {
        if ($paginate) {
            $pages = array_chunk($this->items, $perPage, true);

            $items = $pages[$page - 1] ?? [];   
            $lastPage = count($pages);
            $total = count($this->items);
        } else {
            $items = array_slice($this->items, 0, $perPage, true);

            $page = 1;
            $lastPage = 1;
            $total = count($items);
        }

        return new GridData($items, $page, $lastPage, $perPage, $total);
    }
}
