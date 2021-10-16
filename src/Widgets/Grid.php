<?php

namespace Luischavez\Livewire\Extensions\Widgets;

use Illuminate\Contracts\View\View;
use Luischavez\Livewire\Extensions\ExtendedComponent;
use Luischavez\Livewire\Extensions\Grid\Gridable;
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
     * @var mixed
     */
    public mixed $items = null;

    /**
     * Current page index.
     *
     * @var integer
     */
    public int $page = 1;

    /**
     * Page count.
     *
     * @var integer
     */
    public int $pages = 1;

    /**
     * Items per page.
     *
     * @var integer
     */
    public int $perPage = 10;

    /**
     * Additional items per page to fill the grid.
     *
     * @var integer
     */
    public int $additionalPerPage = 0;

    /**
     * Total items.
     *
     * @var integer
     */
    public int $total = 0;

    /**
     * Paginate results.
     *
     * @var boolean
     */
    public bool $paginate = true;

    /**
     * Justify.
     *
     * @var string
     */
    public string $justify = 'center';

    /**
     * Gap between items.
     *
     * @var float
     */
    public float $gap = 0;

    /**
     * Filters.
     *
     * @var array
     */
    public array $filters = [];

    /**
     * Gridable instance.
     *
     * @var Gridable|null
     */
    protected ?Gridable $gridableInstance = null;

    /**
     * Protected properties.
     *
     * @var array
     */
    protected array $protectedProperties = [
        '*',
    ];

    /**
     * Mount.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->refreshGridableData();
    }

    /**
     * Render.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire-ext::widgets.grid');
    }


    /**
     * Gets the gridable instance.
     *
     * @return Gridable
     */
    protected function gridableInstance(): Gridable
    {
        if ($this->gridableInstance === null) {
            /**
             * @var Gridable
             */
            $this->gridableInstance = TypeFinder::makeOrThrow('grids', $this->gridable);
        }

        return $this->gridableInstance;
    }

    /**
     * Refresh the grid data.
     *
     * @return void
     */
    protected function refreshGridableData(): void
    {
        $this->gridableInstance()->applyFilters($this->filters);
        $data = $this->gridableInstance()->data($this->page, $this->perPage + $this->additionalPerPage);

        $this->items = $data->items;
        $this->pages = $data->pages;
        $this->total = $data->total;
    }

    /**
     * Change the current page.
     *
     * @param integer $page page
     * @return void
     */
    public function changePage(int $page): void
    {
        $this->page = $page;
        $this->refreshGridableData();
    }

    /**
     * Go to next page.
     *
     * @return void
     */
    public function nextPage(): void
    {
        $page = $this->page + 1;
        $page = $page > 1 && $page > $this->pages ? $this->pages : $page;
        $this->changePage($page);
    }

    /**
     * Go to prev page.
     *
     * @return void
     */
    public function prevPage(): void
    {
        $page = $this->page - 1;
        $page = $page < 1 ? 1 : $page;
        $this->changePage($page);
    }

    /**
     * Apply filters.
     *
     * @param string    $name   filter name
     * @param mixed     $value  value
     * @return void
     */
    public function applyFilter(string $name, mixed $value): void
    {
        $this->filters[$name] = $value;

        $this->changePage(1);
    }

    /**
     * Fill the grid with items to keep it balanced.
     *
     * @param float $gridWidth  width of the grid
     * @param float $itemWidth  width of each item
     * @return void
     */
    public function fillGrid(float $gridWidth, float $itemWidth): void
    {
        $gridWidth += $this->gap;
        $itemWidth += $this->gap;

        $currentItemCount = count($this->items);
        $desiredItemCount = $this->perPage;
        $itemCount = $currentItemCount > $desiredItemCount
            ? $desiredItemCount
            : $currentItemCount;

        $itemsPerRow = floor($gridWidth / $itemWidth);
        $itemsOnLastRow = $itemCount % $itemsPerRow;

        $additionalPerPage = 0;

        if ($itemsOnLastRow > 0 && $itemsPerRow != $itemsOnLastRow) {
            $additionalPerPage = $itemCount + $itemsPerRow - $itemsOnLastRow - $this->perPage;
        } else {
            $additionalPerPage = abs($itemsPerRow - $this->perPage);
        }

        if ($additionalPerPage >= $itemsPerRow) {
            $additionalPerPage = 0;
        }

        // P = Page, PP = PerPage, AP = Additinoal, FI = First visible item
        // ((P - 1) * (PP + AP)) + 1 = FI
        // P = ?
        // (P - 1) * (PP + AP) = FI - 1
        // P - 1 = (FI - 1) / (PP + AP)
        // P = ((FI - 1) / (PP + AP)) + 1
        $currentFirstVisibleItem = (($this->page - 1) * ($this->perPage + $this->additionalPerPage)) + 1;

        $this->additionalPerPage = $additionalPerPage;

        $this->page = (($currentFirstVisibleItem - 1) / ($this->perPage + $this->additionalPerPage)) + 1;

        $this->refreshGridableData();
    }

    /**
     * Run on change page event.
     *
     * @param integer $page page
     * @return void
     */
    public function onChangePage(int $page): void
    {
        $this->changePage($page);
    }

    /**
     * Run on next page event.
     *
     * @return void
     */
    public function onNextPage(): void
    {
        $this->nextPage();
    }

    /**
     * Run on prev page event.
     *
     * @return void
     */
    public function onPrevPage(): void
    {
        $this->prevPage();
    }

    /**
     * Run on apply filters event.
     *
     * @param string    $name   filter name
     * @param mixed     $value  value
     * @return void
     */
    public function onApplyFilter(string $name, mixed $value): void
    {
        $this->applyFilter($name, $value);
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'widgets-grid';        
    }
}
