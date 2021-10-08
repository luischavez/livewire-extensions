<?php

namespace Luischavez\Livewire\Extensions\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Iconify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'livewire-ext:iconify {folder} {group} {style} {--name-pattern=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a icon resource file from a source folder';

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
        $folder = $this->argument('folder');
        $group = $this->argument('group');
        $style = $this->argument('style');

        $namePattern = $this->option('name-pattern');

        $this->info("Reading svg files from $folder folder");

        $files = File::files($folder);

        $icons = [];

        foreach ($files as $file) {
            if ($file->getExtension() != 'svg') continue;

            $fileName = $file->getBasename('.svg');
            $iconName = $fileName;

            if ($namePattern) {
                if (preg_match_all("/$namePattern/", $fileName, $matches) !== false) {
                    $iconName = $matches[1][0];
                }
            }

            $this->info("Parsing $fileName svg file to icon $iconName");

            $svg = File::get($file);

            $removeAttributes = [
                'width',
                'height',
            ];

            foreach ($removeAttributes as $attribute) {
                $svg = preg_replace("/$attribute=\".[^\"]*\"|$attribute=\'.[^\']*\'/", '', $svg);
            }

            $svg = preg_replace('/\s+/', ' ', $svg);
            $svg = preg_replace('/\n+/', '', $svg);
            $svg = preg_replace('/>\s+</', '><', $svg);
            $svg = preg_replace('/<\?xml.[^>]*>|<!--.[^>]*-->/', '', $svg);

            $icons[$iconName][$style] = $svg;
        }

        if (!File::exists(resource_path('icons'))) {
            File::makeDirectory(resource_path('icons'));
        }

        $resourceFile = resource_path("icons/$group.php");

        if (File::exists($resourceFile)) {
            $oldIcons = require $resourceFile;
            $icons = array_merge($oldIcons, $icons);
        }

        $icons = var_export($icons, true).";\n";

        $icons = preg_replace('/(array)\s*\(/', '[', $icons);
        $icons = preg_replace('/\),/', '],', $icons);
        $icons = preg_replace('/\)\;/', '];', $icons);
        $icons = preg_replace('/=>[\s\n]*\[/m', '=> [', $icons);

        File::put($resourceFile, "<?php\n\nreturn ".$icons);

        $this->info("Saved to $resourceFile");

        return 0;
    }
}
