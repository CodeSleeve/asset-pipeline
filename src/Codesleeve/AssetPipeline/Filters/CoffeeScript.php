<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use CoffeeScript\Compiler;

class CoffeeScript implements FilterInterface
{
    var $config;
    
    public function __construct($config=array())
    {
	$this->config = $config;
    }

    public function filterLoad(AssetInterface $asset)
    {

    }

    public function filterDump(AssetInterface $asset)
    {
        $content = $asset->getContent();
        
        $config = array_merge( array( 'filename' => $asset->getSourcePath() ), $this->config );

		$content = Compiler::compile($content, $config);

        $asset->setContent($content);
    }
}
