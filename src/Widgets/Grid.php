<?php

namespace Luischavez\Livewire\Extensions\Widgets;

use Illuminate\Contracts\View\View;
use Luischavez\Livewire\Extensions\ExtendedComponent;
use Luischavez\Livewire\Extensions\Gridable;
use Luischavez\Livewire\Extensions\TypeFinder;

/**
 * Grid component.
 */
class Grid extends ExtendedComponent
{
    /**
     * Gridable class name.
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
     * Filters.
     *
     * @var array
     */
    public array $filters = [];

    /**
     * Protected properties.
     *
     * @var array
     */
    protected array $protectedProperties = [
        'gridable',
        'items',
        'filters',
    ];

    /**
     * Mount.
     *
     * @return void
     */
    public function mount(): void
    {
        /**
         * @var Gridable
         */
        $gridableInstance = TypeFinder::makeOrThrow('grids', $this->gridable);
        $this->items = $gridableInstance->items();
    }

    /**
     * Render.
     *
     * @return View
     */
    public function render(): View
    {
        /**
         * @var Gridable
         */
        $gridableInstance = TypeFinder::makeOrThrow('grids', $this->gridable);

        return view('livewire-ext::widgets.grid', [
            'itemComponentName' => $gridableInstance->component(),
        ]);
    }

    /**
     * Run on filter applied.
     *
     * @param string    $name   filter name
     * @param mixed     $value  value
     * @return void
     */
    public function onApplyFilter(string $name, mixed $value): void
    {
        $this->filters[$name] = $value;

        /**
         * @var Gridable
         */
        $gridableInstance = TypeFinder::makeOrThrow('grids', $this->gridable);
        $gridableInstance->applyFilters($this->filters);

        $this->items = $gridableInstance->items();
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'widgets-grid';        
    }
}
