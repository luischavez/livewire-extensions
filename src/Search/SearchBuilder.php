<?php

namespace Luischavez\Livewire\Extensions\Search;

use Illuminate\Database\Eloquent\Builder;

/**
 * Search builder.
 */
class SearchBuilder
{
    /**
     * Query string.
     * 
     * @var string
     */
    protected string $queryString;

    /**
     * Eloquent query builder.
     *
     * @var Builder
     */
    protected Builder $queryBuilder;

    /**
     * Id column on the model.
     *
     * @var string
     */
    protected string $idColumn = 'id';

    /**
     * Text column on the model.
     *
     * @var string
     */
    protected string $textColumn = 'id';

    /**
     * Lookup columns.
     *
     * @var array
     */
    protected array $columns = [];

    /**
     * Item view.
     *
     * @var string|null
     */
    protected ?string $view = null;

    /**
     * Constructor.
     *
     * @param Builder $queryBuilder
     */
    public function __construct(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Set the id column.
     *
     * @param string $idColumn
     * @return self
     */
    public function withId(string $idColumn): self
    {
        $this->idColumn = $idColumn;
        return $this;
    }

    /**
     * Set the text column.
     *
     * @param string $textColumn
     * @return self
     */
    public function withText(string $textColumn): self
    {
        $this->textColumn = $textColumn;
        return $this;
    }

    /**
     * Set the lookup columns.
     *
     * @param array $columns
     * @return self
     */
    public function withColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Set the item view.
     *
     * @param string $view
     * @return self
     */
    public function withView(string $view): self
    {
        $this->view = $view;
        return $this;
    }

    /**
     * String to be searched on the model.
     *
     * @param string $queryString
     * @return self
     */
    public function queryString(string $queryString): self
    {
        $this->queryString = $queryString;
        return $this;
    }

    /**
     * Apply a query filters on this search.
     *
     * @param callable $apply
     * @return self
     */
    public function query(callable $apply): self
    {
        $apply($this->queryBuilder);
        return $this;
    }

    /**
     * Executes the search and return the results.
     *
     * @param integer   $perPage
     * @param integer   $page
     * @return SearchResults
     */
    public function results(int $perPage = 0, int $page = 1): SearchResults
    {
        $queryString = strtolower($this->queryString);

        $this->queryBuilder->where(function(Builder $query) use ($queryString) {
            foreach ($this->columns as $column) {
                $query->orWhere($column, 'like', "%$queryString%");
            }
        });

        $results = $perPage > 0
            ? $this->queryBuilder->paginate($perPage, ['*'], 'page', $page)
            : $this->queryBuilder->get();

        $items = $perPage > 0 ? $results->getCollection() : $results;
        $total = $perPage > 0 ? $results->total() : $results->count();
        $currentPage = $perPage > 0 ? $results->currentPage() : 1;
        $lastPage = $perPage > 0 ? $results->lastPage() : 1;

        $items = $items->map(function ($item) use ($queryString) {
                $fullMatch = false;

                foreach ($this->columns as $column) {
                    if (strtolower($item->{$column}) == $queryString) {
                        $fullMatch = true;
                        break;
                    }
                }

                return [
                    'id'        => $item->{$this->idColumn},
                    'text'      => $item->{$this->textColumn},
                    'view'      => !empty($this->view)
                        ? view($this->view, compact('item'))->render()
                        : $item->{$this->textColumn},
                    'fullMatch' => $fullMatch,
                ];
            })
            ->keyBy($this->idColumn)
            ->sortBy($this->idColumn)
            ->toArray();

        return new SearchResults($queryString, $items, $total, $perPage, $currentPage, $lastPage);
    }
}
