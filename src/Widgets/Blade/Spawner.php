<?php

namespace Luischavez\Livewire\Extensions\Widgets\Blade;

use Illuminate\View\Component;

/**
 * Spawner widget.
 */
class Spawner extends Component
{
    /**
     * Spawner name.
     *
     * @var string
     */
    public string $name;

    /**
     * Parent id (HTML).
     *
     * @var string|null
     */
    public ?string $parentId = null;

    /**
     * Blur background.
     *
     * @var boolean
     */
    public bool $blurBackground = false;

    /**
     * Disable background events.
     *
     * @var boolean
     */
    public bool $disableBackgroundEvents = false;

    /**
     * Replace previous component on spawn.
     *
     * @var boolean
     */
    public bool $replace = false;

    /**
     * Create a new component instance.
     *
     * @param string        $name                       name
     * @param string|null   $parentId                   parent id
     * @param boolean       $blurBackground             blur background
     * @param boolean       $disableBackgroundEvents    disables background events
     * @param boolean       $replace                    replace previous component on spawn
     */
    public function __construct(string $name, ?string $parentId = null,
        bool $blurBackground = false, bool $disableBackgroundEvents = false,
        bool $replace = false)
    {
        $this->name = $name;
        $this->parentId = $parentId;
        $this->blurBackground = $blurBackground;
        $this->disableBackgroundEvents = $disableBackgroundEvents;
        $this->replace = $replace;
    }

    /**
     * @inheritDoc
     */
    public function render(): mixed
    {
        return view('livewire-ext::widgets.blade.spawner');
    }
}
