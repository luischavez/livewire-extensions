<?php

namespace Luischavez\Livewire\Extensions\Inputs;

use Illuminate\Database\Eloquent\Builder;

/**
 * Eloquent input model.
 */
class EloquentInput extends ArrayInput
{
    /**
     * Eloquent model class.
     * 
     * @var string|null
     */
    public ?string $model = null;

    /**
     * Kind of search.
     * 
     * @var string|null
     */
    public ?string $kind = null;

    /**
     * Except ids.
     * 
     * @var array|null
     */
    public ?array $except = null;

    /**
     * Protected properties.
     *
     * @var array
     */
    protected array $protectedProperties = [
        'model',
        'kind',
        'except',
    ];

    /**
     * @inheritDoc
     */
    protected function options(): array
    {
        return $this->search('', 0);
    }

    /**
     * @inheritDoc
     */
    protected function search(string $term, int $maxItems): array
    {
        $options = [];

        $page = 1;

        $options = $this->model::search($this->kind, $term)
            ->query(function (Builder $query) {
                $except = $this->except ?? [];
                
                if ($this->multiple) {
                    $except = array_merge($except, array_keys($this->value));
                }

                $query->whereNotIn((new $this->model)->getkeyName(), $except);
            })
            ->results($maxItems, $page)
            ->toArray();

        return $options['items'];
    }

    /**
     * @inheritDoc
     */
    protected function itemValue(mixed $id, mixed $item): mixed
    {
        return $item['text'];
    }

    /**
     * @inheritDoc
     */
    protected function renderItem(mixed $id, mixed $item): string
    {
        return $item['view'];
    }
}
