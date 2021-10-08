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
     * @inheritDoc
     */
    public function items(): array
    {
        return $this->query->get()->toArray();
    }

    /**
     * @inheritDoc
     */
    protected function filterParameters(): array
    {
        return [$this->query];
    }
}
