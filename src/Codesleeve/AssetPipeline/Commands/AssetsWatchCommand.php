<?php namespace Codesleeve\AssetPipeline\Commands;

use App;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AssetsWatchCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'assets:watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Watches files and folders and runs the events in your pipeline config";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $asset = App::make('asset');
        $config = $asset->getConfig();

        $realpaths = array();
        $base = $config['base_path'];
        $paths = $config['watcher_paths'];
        $events = $config['watcher_events'];

        foreach ($paths as $path)
        {
            $realpath = realpath($base . '/'. $path);

            if ($realpath) {
                $realpaths[] = $realpath;
            }
        }

        $watcher = new Watcher($realpaths, $events);
        $watcher->start();
    }
}