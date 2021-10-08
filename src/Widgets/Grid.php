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
     * Data.
     *
     * @var array
     */
    public array $data = [];

    /**
     * Filters.
     *
     * @var array
     */
    public array $filters = [];

    /**
     * Items per page.
     *
     * @var integer
     */
    public int $perPage = 10;

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
        'gridable',
        'data',
        'filters',
        'perPage',
    ];

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
        $page = empty($this->data) ? 1 : $this->data['page'];
        $this->data = $this->gridableInstance()->items($page, $this->perPage);
    }

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
        return view('livewire-ext::widgets.grid', [
            'itemComponentName' => $this->gridableInstance()->component(),
        ]);
    }

    /**
     * Change the current page.
     *
     * @param integer $page page
     * @return void
     */
    public function changePage(int $page): void
    {
        $this->data = $this->gridableInstance()->items($page, $this->perPage);
    }

    /**
     * Go to next page.
     *
     * @return void
     */
    public function nextPage(): void
    {
        $page = empty($this->data) ? 1 : $this->data['page'] + 1;
        $page = $page > 1 && $page > $this->data['pages'] ? $this->data['pages'] : $page;
        $this->changePage($page);
    }

    /**
     * Go to prev page.
     *
     * @return void
     */
    public function prevPage(): void
    {
        $page = empty($this->data) ? 1 : $this->data['page'] - 1;
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

        $this->gridableInstance()->applyFilters($this->filters);
        $this->changePage(1);
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
