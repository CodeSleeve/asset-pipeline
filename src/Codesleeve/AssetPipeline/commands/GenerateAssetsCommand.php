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
        $structure = __DIR__ . '/../../../../structure';
        $basePath = base_path() . '/' . \Config::get('assetPipeline::path');

        if (!is_dir("$basePath") && mkdir("$basePath", 0755, true))
        {
            $this->line("Creating $basePath");
            $this->xcopy(realpath($structure), realpath($basePath));
            $this->line("Copied some cool assets in there for you. Remove what you don't want.");
        } else {
            $this->line("The assets folder $basePath alerady exists, so I'm not doing anything.");
        }

        $this->line("Finished. Have a nice day!");
    }

    private function xcopy($source, $dest)
    {
        foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}