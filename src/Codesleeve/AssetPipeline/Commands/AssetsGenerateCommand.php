<?php namespace Codesleeve\AssetPipeline\Commands;

use Illuminate\Console\Command;
use Codesleeve\Sprockets\StaticFilesGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AssetsGenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'assets:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generate static assets in your public folder";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $asset = \App::make('asset');

        // we need to turn on concatenation
        // since we are spitting out assets

        $config = $asset->getConfig();
        $config['environment'] = $this->option('env') ? : 'production';
        $asset->setConfig($config);

        $generator = new StaticFilesGenerator($asset->getGenerator());

        $generated = $generator->generate(public_path($config['routing']['prefix']));

        foreach ($generated as $file) {
            $this->line($file);
        }

        $this->line('Finished. Have a nice day! :)');
    }
}
