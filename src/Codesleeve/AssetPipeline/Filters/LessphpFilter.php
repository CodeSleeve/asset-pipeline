<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use lessc;

class LessphpFilter implements FilterInterface
{
    public function __construct()
    {

    }

    public function filterLoad(AssetInterface $asset)
    {

    }
 
    public function filterDump(AssetInterface $asset)
    {       
        $content = $asset->getContent();

        $parser = new lessc();
       
		$content = $parser->parse($content);

        $asset->setContent($content);
    }
}