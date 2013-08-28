<?php
 
namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class MinifyJS implements FilterInterface
{
	private $app;
	private $environments;

	/**
	 * [initialize description]
	 * @param  {[type]} $app [description]
	 * @return {[type]}      [description]
	 */
	public function initialize($app)
	{
		$this->app = $app;
	}

	/**
	 * [__construct description]
	 * @param  string|array $environments list of environments we should minify on
	 * @return null
	 */
	public function __construct($environments = 'production')
	{
		$this->environments = !is_array($environments) ? array($environments) : $environments;
	}

	/**
	 * [filterLoad description]
	 * @param  AssetInterface $asset [description]
	 * @return [type]                [description]
	 */
    public function filterLoad(AssetInterface $asset)
    {

    }
 
 	/**
 	 * [filterDump description]
 	 * @param  AssetInterface $asset [description]
 	 * @return [type]                [description]
 	 */
    public function filterDump(AssetInterface $asset)
    {
    	if ($this->shouldMinify()) {
			$asset->setContent(\JSMinPlus::minify($asset->getContent()) . ';');    		
    	}
    }

    /**
     * [shouldMinify description]
     * @return {[type]} [description]
     */
	protected function shouldMinify()
	{
		return in_array($this->app['env'], $this->environments);
	}

}
