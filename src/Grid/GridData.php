<?php

namespace Luischavez\Livewire\Extensions\Grid;

/**
 * Grid result.
 */
class GridData
{
    /**
     * Items.
     *
     * @var mixed
     */
    public mixed $items;

    /**
     * Current page index.
     *
     * @var integer
     */
    public int $page;

    /**
     * Page count.
     *
     * @var integer
     */
    public int $pages;

    /**
     * Items per page.
     *
     * @var integer
     */
    public int $perPage;

    /**
     * Total items.
     *
     * @var integer
     */
    public int $total;

    /**
     * Constructor.
     *
     * @param mixed     $items      items
     * @param integer   $page       current page
     * @param integer   $pages      page count
     * @param integer   $perPage    items per page
     * @param integer   $total      total items
     */
    public function __construct(mixed $items, int $page, int $pages, int $perPage, int $total)
    {
        $this->items = $items;
        $this->page = $page;
        $this->pages = $pages;
        $this->perPage = $perPage;
        $this->total = $total;
    }
}
