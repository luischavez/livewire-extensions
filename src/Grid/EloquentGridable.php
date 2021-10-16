<?php

namespace Luischavez\Livewire\Extensions\Grid;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Eloquent gridable.
 */
abstract class EloquentGridable extends Gridable
{
    /**
     * Model class name.
     *
     * @var string
     */
    protected string $model;

    /**
     * Query builder.
     *
     * @var Builder
     */
    protected Builder $query;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->query = $this->model::query();
    }

    /**
     * @inheritDoc
     */
    public function data(int $page = 1, int $perPage = 10): GridData
    {
        $usePaginator = empty($this->query->getQuery()->limit)
            && empty($this->query->getQuery()->offset);
        
        if ($usePaginator) {
            $paginator = $this->query->paginate($perPage, ['*'], null, $page);

            $lastPage = $paginator->lastPage();
            $total = $paginator->total();
            $items = $paginator->getCollection();   
        } else {
            $items = $this->query->get();

            $lastPage = 1;
            $total = $items->count();
        }

        return new GridData($items, $page, $lastPage, $perPage, $total);
    }

    /**
     * @inheritDoc
     */
    public function key(mixed $item): mixed
    {
        return $item->id;
    }

    /**
     * @inheritDoc
     */
    public function itemName(): string
    {
        return Str::camel(class_basename($this->model));
    }

    /**
     * @inheritDoc
     */
    protected function filterParameters(): array
    {
        return [$this->query];
    }
}
