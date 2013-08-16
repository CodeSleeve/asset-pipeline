<?php namespace Codesleeve\AssetPipeline\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AssetsCleanCommand extends Command {

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
    protected $description = "Cleans out the cached assets in production";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        \Cache::forget('asset_pipeline_recently_scanned_javascripts');
        \Cache::forget('asset_pipeline_recently_scanned_stylesheets');
        \Cache::forget('asset_pipeline_javascripts_last_updated_at');
        \Cache::forget('asset_pipeline_stylesheets_last_updated_at');
        \Cache::forget('asset_pipeline_manager');

        $this->line('');
        $this->line('Asset pipeline cache cleared!');
        $this->line('Cache will be re-built on next page request');
        $this->line('');
        $this->line('Finished. Have a nice day! :)');
        $this->line('                          - Codesleeve Team');
    }

}