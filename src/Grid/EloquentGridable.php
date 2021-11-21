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
    public function data(bool $paginate = true, int $perPage = 10, int $page = 1): GridData
    {
        if ($paginate) {
            $paginator = $this->query->paginate($perPage, ['*'], null, $page);

            $lastPage = $paginator->lastPage();
            $total = $paginator->total();
            $items = $paginator->getCollection();   
        } else {
            $items = $this->query->take($perPage)->get();

            $page = 1;
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
    public function itemName(mixed $item): string
    {
        return Str::camel(class_basename($this->model));
    }
}
