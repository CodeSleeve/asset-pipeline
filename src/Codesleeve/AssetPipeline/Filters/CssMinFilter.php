<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class CssMinFilter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset)
    {

    }

    public function filterDump(AssetInterface $asset)
    {
		$asset->setContent(\CssMin::minify($asset->getContent()));
    }
}