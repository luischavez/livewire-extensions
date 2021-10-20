<?php

namespace Luischavez\Livewire\Extensions;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Luischavez\Livewire\Extensions\Commands\Iconify;
use Luischavez\Livewire\Extensions\Commands\MakeAction;
use Luischavez\Livewire\Extensions\Commands\MakeGrid;
use Luischavez\Livewire\Extensions\Commands\MakeInput;
use Luischavez\Livewire\Extensions\Inputs\ArrayInput;
use Luischavez\Livewire\Extensions\Inputs\DateInput;
use Luischavez\Livewire\Extensions\Inputs\EloquentInput;
use Luischavez\Livewire\Extensions\Inputs\IntInput;
use Luischavez\Livewire\Extensions\Inputs\StringInput;
use Luischavez\Livewire\Extensions\Widgets\Alert;
use Luischavez\Livewire\Extensions\Widgets\AuthSystem;
use Luischavez\Livewire\Extensions\Widgets\Blade\Button;
use Luischavez\Livewire\Extensions\Widgets\Blade\Icon;
use Luischavez\Livewire\Extensions\Widgets\Blade\Loading;
use Luischavez\Livewire\Extensions\Widgets\Blade\Spawner;
use Luischavez\Livewire\Extensions\Widgets\Dialog;
use Luischavez\Livewire\Extensions\Widgets\Grid;
use Luischavez\Livewire\Extensions\Widgets\SmartInput;

/**
 * Service provider for livewire-extensions package.
 */
class LivewireExtensionsServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/livewire-ext.php' => config_path('livewire-ext.php'),
        ], ['livewire-ext', 'livewire-ext:config']);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/livewire-ext'),
        ], ['livewire-ext', 'livewire-ext:views']);

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/livewire-ext'),
        ], ['livewire-ext', 'livewire-ext:lang']);

        $this->publishes([
            __DIR__.'/../resources/icons' => resource_path('icons'),
        ], ['livewire-ext', 'livewire-ext:icons']);

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'livewire-ext');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'livewire-ext');

        $this->loadViewComponentsAs('widgets', [
            Button::class,
            Icon::class,
            Loading::class,
            Spawner::class,
        ]);

        Livewire::component(AuthSystem::getName(), AuthSystem::class);
        Livewire::component(Alert::getName(), Alert::class);
        Livewire::component(Dialog::getName(), Dialog::class);
        Livewire::component(Grid::getName(), Grid::class);
        Livewire::component(SmartInput::getName(), SmartInput::class);

        TypeFinder::register('inputs', 'array', ArrayInput::class);
        TypeFinder::register('inputs', 'date', DateInput::class);
        TypeFinder::register('inputs', 'eloquent', EloquentInput::class);
        TypeFinder::register('inputs', 'int', IntInput::class);
        TypeFinder::register('inputs', 'string', StringInput::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Iconify::class,
                MakeAction::class,
                MakeGrid::class,
                MakeInput::class,
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/livewire-ext.php', 'livewire-ext',
        );
    }
}
