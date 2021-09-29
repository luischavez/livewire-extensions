<?php

namespace Luischavez\Livewire\Extensions\Widgets\Blade;

use Illuminate\View\Component;
use Throwable;

/**
 * Icon widget.
 */
class Icon extends Component
{
    /**
     * Icon name.
     *
     * @var string
     */
    public string $name;

    /**
     * SVG representation for this icon.
     * 
     * @var string
     */
    public string $svg;

    /**
     * Icon style.
     * 
     * @var string
     */
    public string $style;

    /**
     * Icon group.
     * 
     * @var string
     */
    public string $group;

    /**
     * Default icon if the requested icon is not found.
     *
     * @var string
     */
    protected string $defaultIcon;

    /**
     * Create a new component instance.
     *
     * @param string    $name
     * @param string    $style
     * @param string    $group
     */
    public function __construct(string $name, ?string $style = null, ?string $group = null)
    {
        $this->name = $name;
        $this->style = $style ?? 'solid';
        $this->group = $group ?? 'heroicons';

        $groups = config('livewire-ext.icons');

        if (!in_array($this->group, $groups)) {
            $this->svg = '';
            return;
        }

        try {
            $icons = require resource_path("icons/{$this->group}.php");
        } catch (Throwable $ex) {
            $icons = require __DIR__."../../../../../../../resources/icons/{$this->group}.php";
        }

        if (isset($icons[$this->name])) {
            $this->svg = $icons[$this->name][$this->style] ?? '';
        } else {
            $this->svg = '';
        }
    }

    /**
     * @inheritDoc
     */
    public function render(): mixed
    {
        return view('livewire-ext::widgets.blade.icon');
    }
}
