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
    public function items(int $page = 1, int $perPage = 10): array
    {
        $pages = array_chunk($this->items, $perPage, true);

        $items = $pages[$page] ?? [];

        return [
            'items'     => $items,
            'page'      => $page,
            'pages'     => count($pages),
            'perPage'   => $perPage,
            'total'     => count($items),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function filterParameters(): array
    {
        return [$this->items];
    }
}
