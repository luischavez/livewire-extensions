<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Illuminate\Support\Str;
use Luischavez\Livewire\Extensions\Search\SearchBuilder;

/**
 * Enables search on eloquent models.
 */
trait WithSearch
{
    /**
     * Performs a search on the model.
     *
     * @param string $kind
     * @param string $queryString
     * @return SearchBuilder
     */
    public static function search(string $kind, string $queryString): SearchBuilder
    {
        $kind = Str::studly($kind);
        $searchMethod = "search$kind";

        /**
         * @var SearchBuilder
         */
        $searchBuilder = self::{$searchMethod}();
        $searchBuilder->queryString($queryString);

        return $searchBuilder;
    }

    /**
     * Creates a new search builder.
     *
     * @return SearchBuilder
     */
    public static function searchBuilder(): SearchBuilder
    {
        return new SearchBuilder(self::query());
    }
}
