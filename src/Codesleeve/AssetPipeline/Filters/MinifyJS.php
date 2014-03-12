<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class MinifyJS implements FilterInterface
{
	public function __construct($env = 'production', $environments = 'production')
	{
		$this->environments = !is_array($environments) ? array($environments) : $environments;
        $this->env = $env;
	}

    public function filterLoad(AssetInterface $asset)
    {

    }

    public function filterDump(AssetInterface $asset)
    {
    	if (in_array($this->env, $this->environments)) {
			$asset->setContent(\JSMinPlus::minify($asset->getContent()) . ';');
    	}
    }
}
