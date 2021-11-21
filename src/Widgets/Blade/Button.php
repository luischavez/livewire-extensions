<?php

namespace Luischavez\Livewire\Extensions\Widgets\Blade;

use Illuminate\View\Component;

/**
 * Button component.
 */
class Button extends Component
{
    const DEFAULT = 'default';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const INFO = 'info';
    const DANGER = 'danger';

    /**
     * Button type.
     * 
     * @var string
     */
    public string $type = self::DEFAULT;

    /**
     * Icon.
     * 
     * @var string|null
     */
    public ?string $icon;

    /**
     * Icon Group.
     * 
     * @var string|null
     */
    public ?string $group;

    /**
     * inverted icon.
     * 
     * @var bool
     */
    public bool $inverted;

    /**
     * Disable this button.
     * 
     * @var bool
     */
    public bool $disabled;

    /**
     * Create a new component instance.
     *
     * @param string        $type
     * @param string|null   $icon
     * @param string|null   $group
     * @param bool          $inverted
     * @param bool          $disabled
     */
    public function __construct(string $type = '',
        ?string $icon = null,
        ?string $group = 'heroicons',
        bool $inverted = false,
        bool $disabled = false)
    {
        $this->type = $type;
        $this->icon = $icon;
        $this->group = $group;
        $this->inverted = $inverted;
        $this->disabled = $disabled;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        return view('livewire-ext::widgets.blade.button');
    }
}
