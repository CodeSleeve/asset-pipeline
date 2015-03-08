<?php namespace Codesleeve\AssetPipeline\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AssetsSetupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'assets:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Setup the default asset pipeline folders in your new Laravel project";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $structure = __DIR__ . '/../../../../structure';
        $base = base_path();

        $this->line('<info>codesleeve\asset-pipeline creating initial directory structure and copying some general purpose assets over</info>');

        $this->xcopy(realpath($structure), realpath($base));

        $this->line('');
        $this->line('<info>codesleeve\asset-pipeline completed directory setup</info>');
    }

    private function xcopy($source, $dest)
    {
        $base = base_path();
        foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                if (!is_dir($dest . '/' . $iterator->getSubPathName())) {
                    mkdir($dest . '/' . $iterator->getSubPathName());
                }
            } else {
                copy($item, $dest . '/' . $iterator->getSubPathName());
                $this->line('   Copying -> ' . str_replace($base, '', $dest . '/' . $iterator->getSubPathName()));
            }
        }
    }
}
