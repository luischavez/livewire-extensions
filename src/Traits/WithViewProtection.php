<?php

namespace Luischavez\Livewire\Extensions\Traits;

use Livewire\CreateBladeView;
use Livewire\Livewire;

/**
 * Enables view protection.
 */
trait WithViewProtection
{
    use WithAuth;

    /**
     * Livewire component name.
     *
     * @var string|null
     */
    protected ?string $noAuthLivewireComponentName = null;

    /**
     * View name.
     *
     * @var string|null
     */
    protected ?string $noAuthViewName = 'livewire-ext::auth.not-logged';

    /**
     * Raw view data.
     *
     * @var string|null
     */
    protected ?string $noAuthRaw = null;

    /**
     * Render the component and validates the authentication status.
     *
     * 
     */
    public function renderToView()
    {
        $logged = auth()->check();

        if ($logged ||
            ($this->noAuthLivewireComponentName === null && $this->noAuthViewName === null && $this->noAuthRaw === null))
            return parent::renderToView();

        if ($this->shouldSkipRender) return null;

        Livewire::dispatch('component.rendering', $this);

        $view = null;

        if ($this->noAuthLivewireComponentName !== null) {
            $view = app('view')->make(
                CreateBladeView::fromString("<livewire:is component=\"{$this->noAuthLivewireComponentName}\" />"));
        } else if ($this->noAuthViewName !== null) {
            $view = app('view')->make($this->noAuthViewName);
        } else if ($this->noAuthRaw !== null) {
            $view = app('view')->make(CreateBladeView::fromString($this->noAuthRaw));
        }

        // Get the layout config from the view.
        if ($view->livewireLayout) {
            $this->initialLayoutConfiguration = $view->livewireLayout;
        }

        Livewire::dispatch('component.rendered', $this, $view);

        return $this->preRenderedView = $view;
    }
}
