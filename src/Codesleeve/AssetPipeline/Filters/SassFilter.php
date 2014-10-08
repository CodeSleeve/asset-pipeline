<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

use SassParser;

class SassFilter implements FilterInterface
{
	public function filterLoad(AssetInterface $asset)
	{

	}

	public function filterDump(AssetInterface $asset)
	{
		$file = $asset->getSourceRoot() . '/' . $asset->getSourcePath();

		$options = array(
			'style'=>'expanded',
		);

		$sass = new SassParser($options);

		$parsed = $sass->toCss($file);

		$asset->setContent($parsed);
	}
}