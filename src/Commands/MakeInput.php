<?php

namespace Luischavez\Livewire\Extensions\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeInput extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livewire-ext:input {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new input';

    /**
     * @inheritDoc
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/Input.stub';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return "$rootNamespace\Inputs";
    }
}
