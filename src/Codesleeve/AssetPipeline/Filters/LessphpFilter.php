<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use lessc;

class LessphpFilter implements FilterInterface
{
    private $base = array();
    private $paths = array();

    public function __construct()
    {

    }

    public function setAssetPipeline($pipeline)
    {
        $config = $pipeline->getConfig();

        $this->base = $config['base_path'];
        $this->paths = $config['paths'];
    }

    public function filterLoad(AssetInterface $asset)
    {

    }
 
    public function filterDump(AssetInterface $asset)
    {       
        $content = $asset->getContent();

        $parser = $this->lessParser();

		$content = $parser->parse($content);

        $asset->setContent($content);
    }

    protected function lessParser()
    {
        $parser = new lessc();

        return $parser;
    }
}