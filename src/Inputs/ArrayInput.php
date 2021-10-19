<?php

namespace Luischavez\Livewire\Extensions\Inputs;

use Illuminate\Contracts\View\View;

/**
 * Array input.
 */
class ArrayInput extends Input
{
    /**
     * Maxiumum options to show.
     * 
     * @var int
     */
    public int $maxItems = 5;

    /**
     * Visible items on scroll view.
     * 
     * @var int
     */
    public int $visibleItems = 5;

    /**
     * Visible dropdown status.
     * 
     * @var bool
     */
    public bool $closed = true;

    /**
     * Enables multiple value selection.
     * 
     * @var bool
     */
    public bool $multiple = false;

    /**
     * Enables search.
     * 
     * @var bool
     */
    public bool $searchEnabled = false;

    /**
     * Trigger the search on empty input.
     * 
     * @var bool
     */
    public bool $searchOnEmpty = false;

    /**
     * Search term.
     * 
     * @var string
     */
    public string $searchTerm = '';

    /**
     * Available options.
     * 
     * @var array
     */
    public array $options = [];

    /**
     * Indicates if the search was submitted.
     *
     * @var boolean
     */
    public bool $submitted = false;

    /**
     * Input placeholder.
     * 
     * @var string|null
     */
    public ?string $placeholder = null;

    /**
     * Protected properties.
     * 
     * @var array
     */
    protected array $protectedProperties = [
        'maxItems',
        'visibleItems',
        'multiple',
        'searchEnabled',
        'searchOnEmpty',
        'options',
        'submitted',
        'value',
    ];

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function mount(): void
    {
        if ($this->multiple) {
            if (!is_array($this->value)) {
                $this->value = [];
            }

            $this->searchEnabled = false;
        }

        if (empty($this->options) && !$this->searchEnabled) {
            $this->load();
            $this->closed = true;
        }

        if ($this->value !== null) {
            if (is_array($this->value)) {
                $value = $this->value;
                $this->value = [];
                foreach ($value as $id) {
                    $this->select($id);
                }
            } else {
                $this->select($this->value);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function render(): ?View
    {
        return view('livewire-ext::inputs.array', [
            'items' => $this->renderItems(),
        ]);
    }

    /**
     * Array options.
     *
     * @return array
     */
    protected function options(): array
    {
        return $this->options;
    }

    /**
     * Perform a search on the options.
     *
     * @param string    $term
     * @param int       $maxItems
     * @return array
     */
    protected function search(string $term, int $maxItems): array
    {
        $options = $this->options();

        foreach ($options as $id => $item) {
            $value = $this->itemValue($id, $item);

            if (strpos(strtolower($value), strtolower($term)) === false) {
                unset($options[$id]);
            }
        }

        $options = array_slice($options, 0, $maxItems, true);

        return $options;
    }

    /**
     * Gets the item value.
     *
     * @param mixed $id
     * @param mixed $item
     * @return mixed
     */
    protected function itemValue(mixed $id, mixed $item): mixed
    {
        return $item;
    }

    /**
     * Render the item.
     *
     * @param mixed $id
     * @param mixed $item
     * @return string
     */
    protected function renderItem(mixed $id, mixed $item): string
    {
        return strval($item);
    }

    /**
     * Render all the items.
     *
     * @return array
     */
    protected function renderItems(): array
    {
        $items = [];

        foreach ($this->options as $key => $option) {
            $items[$key] = $this->renderItem($key, $option);
        }

        return $items;
    }

    /**
     * Search the term on the options on term updated.
     *
     * @param string $searchTerm
     * @return void
     */
    protected function searchByTerm(string $searchTerm): void
    {
        if (!$this->searchEnabled) return;

        $this->searchTerm = $searchTerm;

        if (empty($this->searchTerm) && !$this->searchOnEmpty) return;

        $this->load();
    }

    /**
     * Loads the options.
     *
     * @return void
     */
    public function load(): void
    {
        if ($this->searchEnabled) {
            $this->submitted = false;
        }

        if ($this->searchEnabled && !$this->searchOnEmpty && empty($this->searchTerm)) {
            return;
        }

        $this->options = $this->searchEnabled
            ? $this->search($this->searchTerm, $this->maxItems)
            : $this->options();

        $this->closed = empty($this->options);

        if ($this->multiple) {
            foreach ($this->options as $id => $value) {
                if (isset($this->value[$id])) {
                    unset($this->options[$id]);
                }
            }
        } else {
            if (count($this->options) == 1) {
                $id = array_key_first($this->options);
                $value = $this->itemValue($id, $this->options[$id]);

                if ($this->searchEnabled) {
                    if (strtolower($this->searchTerm) == strtolower($value)) {
                        $this->select($id);
                    } else {
                        if ($this->value !== null) {
                            $this->unselect($this->value);
                        }
                    }
                } else {
                    $this->select($id);
                }
            }
        }
    }

    /**
     * @inheritDoc
     *
     * @param string    $key    property name
     * @param mixed     $value  property value
     * @return void
     */
    public function updated(string $key, mixed $value): void
    {
        if ($key == 'searchTerm') {
            $this->searchByTerm($value);
        }
    }

    /**
     * Select a id from the options.
     *
     * @param mixed $id
     * @return void
     */
    public function select(mixed $id): void
    {
        $value = $id === null ? null : $this->itemValue($id, $this->options[$id]);

        if ($this->multiple) {
            if (!isset($this->value[$id])) {
                $this->value[$id] = $value;
            }

            ksort($this->value);
        } else {
            $this->value = $id;

            if ($value) {
                $this->searchTerm = $value;
            }

            if ($this->searchEnabled) {
                $this->submitted = true;
            }
        }

        $this->triggerUpdate();

        $this->closed = true;
    }

    /**
     * Removes the id from the selected ids.
     *
     * @param mixed $id
     * @return void
     */
    public function unselect(mixed $id): void
    {
        if ($id === null) {
            return;
        }

        if ($this->multiple) {
            if (isset($this->value[$id])) {
                unset($this->value[$id]);
            }

            ksort($this->value);
        } else {
            $this->value = null;
        }

        $this->triggerUpdate();
    }

    /**
     * @inheritDoc
     */
    public function value(): mixed
    {
        if ($this->multiple) {
            return array_keys($this->value);
        }

        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function extraValues(): array
    {
        $extra = [];

        if ($this->searchEnabled) {
            $extra[] = $this->searchTerm;
            $extra[] = $this->submitted;
        }

        return $extra;
    }

    /**
     * Trigger input updated.
     *
     * @return void
     */
    public function triggerUpdate(): void
    {
        $this->component->updated('proxyData.value', $this->value);
    }
}
