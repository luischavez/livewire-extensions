<?php

namespace Luischavez\Livewire\Extensions\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeGrid extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livewire-ext:grid {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new grid';

    /**
     * @inheritDoc
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/Grid.stub';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return "$rootNamespace\Grids";
    }
}
