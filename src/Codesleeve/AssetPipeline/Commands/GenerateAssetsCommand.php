<?php namespace Codesleeve\AssetPipeline\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateAssetsCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generates the default asset pipeline folders for you";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $basePath = base_path() . '/' . \Config::get('assetPipeline::path');

        if (!is_dir("$basePath/javascripts") && mkdir("$basePath/javascripts", 0755, true)) {
            $this->line("Creating $basePath/javascripts");
        }

        if (!is_dir("$basePath/stylesheets") && mkdir("$basePath/stylesheets", 0755, true)) {
            $this->line("Creating $basePath/stylesheets");
        }

        $this->line("Drop assets in $basePath");
        $this->line("Finished. Have a nice day!");
    }

    /**
     * Get the path to the file that should be generated
     *
     * @return string
     */
    protected function getPath()
    {
//       return $this->option('path') . '/' . strtolower($this->argument('name')) . '.blade.php';
    }

}