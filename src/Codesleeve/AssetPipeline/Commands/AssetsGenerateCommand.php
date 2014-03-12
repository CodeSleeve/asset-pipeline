<?php namespace Codesleeve\AssetPipeline\Commands;

use Illuminate\Console\Command;
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
	$config['environment'] = $this->option('env');
	$asset->setConfig($config);

	$generator = new Codesleeve\Sprockets\StaticFileGenerator($asset->getGenerator());

        $generated = $generator->generate(public_path() . '/' . $config['routing.prefix']);

        foreach ($generated as $file)
        {
            $this->line($file);
        }

        $this->line('Finished. Have a nice day! :)');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
		array('env', 'e', InputOption::VALUE_OPTIONAL, 'What environment should we generate assets for? Default: production', 'production'),
	);
    }
}
