<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

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

        $parser = new Less_Parser();
		$parser->parse($content);
		$content = $parser->getCss();

        $asset->setContent($content);
    }
}