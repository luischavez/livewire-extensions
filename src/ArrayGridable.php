<?php

namespace Luischavez\Livewire\Extensions;

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
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    protected function filterParameters(): array
    {
        return [$this->items];
    }
}
