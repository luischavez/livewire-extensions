<?php

namespace Luischavez\Livewire\Extensions\Widgets;

use Luischavez\Livewire\Extensions\ExtendedComponent;

/**
 * Grid component.
 */
class Grid extends ExtendedComponent
{
    /**
     * Item component name
     *
     * @var string
     */
    public string $gridable;

    /**
     * Items.
     *
     * @var array
     */
    public array $items = [];

    /**
     * Protected properties.
     *
     * @var array
     */
    protected array $protectedProperties = [
        'gridable',
        'items',
    ];

    /**
     * Run on filter applied.
     *
     * @param string    $name   filter name
     * @param mixed     $value  value
     * @return void
     */
    public function onApplyFilter(string $name, mixed $value): void
    {

    }
}
