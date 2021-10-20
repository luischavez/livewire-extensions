<?php

namespace Luischavez\Livewire\Extensions\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeAction extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livewire-ext:action {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new action';

    /**
     * @inheritDoc
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/Action.stub';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return "$rootNamespace\Actions";
    }
}
