<?php

namespace Luischavez\Livewire\Extensions\Commands;

use Illuminate\Console\Command;

class MakeSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livewire-ext:search {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new search';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        return 0;
    }
}
