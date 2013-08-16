<?php
namespace Codesleeve\AssetPipeline;

class AssetCacheRepository
{
	/**
	 * [__construct description]
	 * @param  {[type]} $env
	 * @param  {[type]} $cache
	 * @param  {[type]} $config
	 * @param  {[type]} $input
	 * @return {[type]}
	 */
	public function __construct($app)
	{
		$this->env = $app['env'];
		$this->cache = $app['cache'];
		$this->config = $app['config'];
		$this->asset = $app['asset'];
	}

	/**
	 * [javascripts description]
	 * @param  {[type]} $path
	 * @return {[type]}
	 */
	public function javascripts($path)
	{
		return $this->asset->javascripts($path);

		// if ($this->env == "production" && $this->cache->get('asset_pipeline_recently_scanned_javascripts')) {
		// 	return $this->fetch($path, 'javascripts');
		// }

		// scan the asset directories
		//$lastUpdatedAt = $this->asset->lastUpdatedAt($path);

		// // if a file has been changed then lets forget our cache
		// if ($this->cache->get('asset_pipeline_last_updated_at') != $lastUpdatedAt) {
		// 	$this->cache->forever('asset_pipeline_last_updated_at', $lastUpdatedAt);
		// 	$manager[$path] = 
		// }

		// return $this->fetch($path, 'javascripts');
	}

	/**
	 * [stylesheets description]
	 * @param  {[type]} $path
	 * @return {[type]}
	 */
	public function stylesheets($path)
	{
		return $this->asset->stylesheets($path);
		// if ($this->env == "production" && $this->cache->get('asset_pipeline_recently_scanned_stylesheets')) {
		// 	return $this->fetch($path, 'stylesheets');
		// }

		// return $this->fetch($path, 'stylesheets');
	}

	/**
	 * [fetch description]
	 * @param  {[type]} $path
	 * @param  {[type]} $type
	 * @return {[type]}
	 */
	public function fetch($path, $type)
	{
		$manager = $this->manager();

		if (array_key_exists($path, $manager)) {
			return $manager[$path];
		}

		if ($type == 'javascripts') {
			$manager[$path] = $this->asset->javascripts($path);

		}
		if ($type == 'stylesheets') {
			$manager[$path] = $this->asset->stylesheets($path);
		}

		$this->manager($manager);
	}

	/**
	 * [manager description]
	 * @return {[type]}
	 */
	public function manager($manager = null)
	{
 		if (!$this->cache->has('asset_pipeline_manager')) {
			$manager = array();
		}

		if (!is_null($manager)) {
			$this->cache->forever('asset_pipeline_manager', $manager);
		}

		return $this->cache->get('asset_pipeline_manager');
	}

	private $env;
	private $cache;
	private $config;
	private $asset;
}
