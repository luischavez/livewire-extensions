<?php

namespace Luischavez\Livewire\Extensions\Search;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Search results.
 */
class SearchResults implements Arrayable
{
    /**
     * Query string.
     *
     * @var string
     */
    public string $queryString;

    /**
     * Items.
     *
     * @var array
     */
    public array $items;

    /**
     * Total of items.
     *
     * @var int
     */
    public int $total;

    /**
     * Items per page.
     *
     * @var int
     */
    public int $perPage;

    /**
     * Current page.
     *
     * @var int
     */
    public int $currentPage;

    /**
     * Last page.
     *
     * @var int
     */
    public int $lastPage;

    /**
     * Constructor
     *
     * @param string    $queryString
     * @param array     $items
     * @param int       $total
     * @param int       $perPage
     * @param int       $currentPage
     * @param int       $lastPage
     */
    public function __construct(string $queryString,
        array $items, int $total,
        int $perPage, int $currentPage, int $lastPage)
    {
        $this->queryString = $queryString;
        $this->items = $items;
        $this->total = $total;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
        $this->lastPage = $lastPage;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'queryString'   => $this->queryString,
            'items'         => $this->items,
            'total'         => $this->total,
            'perPage'       => $this->perPage,
            'currentPage'   => $this->currentPage,
            'lastPage'      => $this->lastPage,
        ];
    }
}
