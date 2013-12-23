<?php namespace Codesleeve\AssetPipeline\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AssetsCleanCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'assets:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Cleans out all your cached assets";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $files = $this->option('file');
        $asset = \App::make('asset');

        foreach ($files as $file)
        {
            $absolutePath = $asset->parser->absoluteFilePath($file);
            $removed = $asset->generator->cached($absolutePath)->remove();

            if ($removed === false) {
                echo PHP_EOL . "<warning> failed to find/remove cache for {$absolutePath}" . PHP_EOL;
            }
        }

        $this->line('');
        $this->line('Asset pipeline cache cleared!');
        $this->line('Cache will be re-built on next page request');
        $this->line('');
        $this->line('Finished. Have a nice day! :)');
        $this->line('                          - Codesleeve Team');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('file', 'f', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'This is files that will have the assets cleaned.', array('application.js', 'application.css')),
        );
    }
}