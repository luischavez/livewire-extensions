<?php

namespace Luischavez\Livewire\Extensions;

use Illuminate\Database\Eloquent\Builder;

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
     * Key name.
     *
     * @return string
     */
    protected function keyName(): string
    {
        return 'id';
    }

    /**
     * @inheritDoc
     */
    public function items(): array
    {
        $results = $this->query->get();

        $items = [];

        foreach ($results as $item) {
            $items[$item->{$this->keyName()}] = $item;
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    protected function filterParameters(): array
    {
        return [$this->query];
    }
}
