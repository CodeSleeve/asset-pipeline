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
     * Construct a new AssetsCleanCommand
     */
    public function __construct()
    {
        parent::__construct();

        $this->asset = \App::make('asset');

        // force concatenation off, so we can get
        // a big list of all the files required
        // in this manifest file
        $config = $this->asset->getConfig();
        $config['concat'] = array();
        $this->asset->setConfig($config);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $recursive = $this->option('recursive') === true;
        $this->verbose = $this->option('verbose') === true;

        $files = $this->option('file');

        foreach ($files as $file)
        {
            $this->removeAssetCache($file, $recursive);
        }

        $this->line('');
        $this->line('Asset pipeline cache cleared!');
        $this->line('Cache will be re-built on next page request');
        $this->line('');
        $this->line('Finished. Have a nice day! :)');
        $this->line('                          - Codesleeve Team');
    }

    /**
     * Removes the AssetCache for this file
     *
     * @param  [type] $file      [description]
     * @param  [type] $recursive [description]
     * @return [type]            [description]
     */
    protected function removeAssetCache($file, $recursive)
    {
        $files = $this->asset->isJavascript($file) ? $this->asset->getParser()->javascriptFiles($file) : $this->asset->getParser()->stylesheetFiles($file);

        array_push($files, $this->asset->getParser()->absoluteFilePath($file));

        foreach ($files as $file)
        {
            $removed = $this->asset->getGenerator()->cachedFile($file)->remove();

            if ($removed === false) {
                $this->writeln(PHP_EOL . "<warning> failed to find/remove cache for {$file}");
            }
        }
    }

    /**
     * Output messages to the user if verbose is on
     *
     * @param  `[type] $message [description]
     * @return [type]          [description]
     */
    protected function writeln($message)
    {
        if ($this->verbose)
        {
            echo $message . PHP_EOL;
        }
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
            array('recursive', 'r', InputOption::VALUE_OPTIONAL, 'Should we recursively remove all cached files the manifest requires? Default is true', true),
          //  array('verbose', 'v', InputOption::VALUE_OPTIONAL, 'Should we be extra noisy? Default is false', false),
        );
    }
}
