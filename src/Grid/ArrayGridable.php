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
    public function data(int $page = 1, int $perPage = 10): GridData
    {
        $pages = array_chunk($this->items, $perPage, true);

        $items = $pages[$page - 1] ?? [];

        return new GridData($items, $page, count($pages), $perPage, count($items));
    }

    /**
     * @inheritDoc
     */
    protected function filterParameters(): array
    {
        return [$this->items];
    }
}
