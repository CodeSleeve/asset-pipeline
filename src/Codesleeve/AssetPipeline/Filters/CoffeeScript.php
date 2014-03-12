<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use CoffeeScript\Compiler;

class CoffeeScript implements FilterInterface
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

		$content = Compiler::compile($content, array('filename' => $asset->getSourcePath()));

        $asset->setContent($content);
    }
}