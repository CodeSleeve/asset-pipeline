<?php
namespace Codesleeve\AssetPipeline;

/**
 * We are using the following cache keys in Laravel
 *
 *   asset_pipeline_recently_scanned_javascripts
 *   asset_pipeline_recently_scanned_stylesheets
 *   asset_pipeline_javascripts_last_updated_at
 *   asset_pipeline_stylesheets_last_updated_at
 *   asset_pipeline_manager
 *
 * So if we ever to clear the cache, we just need to remove
 * these keys above...
 * 
 */

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
	 * If we are not on production this just gets sent straight to asset pipeline
	 *
	 * Else, in order to keep from hammering the file system we use the cache manager
	 * to see if we have 
	 * 
	 *    1) recently scanned javascripts
	 *    2) when was the last time the javascript $path changed?
	 *    3) update/fetch accordinly
	 *    
	 * @param  {[type]} $path
	 * @return {[type]}
	 */
	public function javascripts($path)
	{
		if ($this->env != "production") {
			return $this->asset->javascripts($path);			
		}

		if ($this->cache->get('asset_pipeline_recently_scanned_javascripts')) {
			return $this->fetch($path, 'javascripts');
		}

		$this->cache->put('asset_pipeline_recently_scanned_javascripts', true, $this->config->get('asset-pipeline::cache'));

		$fullpath = $this->asset->getFullPath($path);
		$lastUpdatedAt = $this->lastUpdatedAt($fullpath);

		// if a file has been changed then lets override our cache
		if ($this->cache->get('asset_pipeline_javascripts_last_updated_at') != $lastUpdatedAt) {
			$this->cache->forever('asset_pipeline_javascripts_last_updated_at', $lastUpdatedAt);
			return $this->fetch($path, 'javascripts', true);
		}

		return $this->fetch($path, 'javascripts');
	}

	/**
	 * If we are not on production this just gets sent straight to asset pipeline
	 *
	 * Else, in order to keep from hammering the file system we use the cache manager
	 * to see if we have
	 * 
	 *    1) recently scanned stylesheets
	 *    2) when was the last time the stylesheet $path changed?
	 *    3) update/fetch accordinly
	 *    
	 * @param  {[type]} $path
	 * @return {[type]}
	 */
	public function stylesheets($path)
	{
		if ($this->env != "production") {
			return $this->asset->stylesheets($path);
		}

		if ($this->cache->get('asset_pipeline_recently_scanned_stylesheets')) {
			return $this->fetch($path, 'stylesheets');
		}

		$this->cache->put('asset_pipeline_recently_scanned_stylesheets', true, $this->config->get('asset-pipeline::cache'));

		$fullpath = $this->asset->getFullPath($path);

		$lastUpdatedAt = $this->lastUpdatedAt($fullpath);

		// if a file has been changed then lets override our cache
		if ($this->cache->get('asset_pipeline_stylesheets_last_updated_at') != $lastUpdatedAt) {
			$this->cache->forever('asset_pipeline_stylesheets_last_updated_at', $lastUpdatedAt);
			return $this->fetch($path, 'stylesheets', true);
		}

		return $this->fetch($path, 'stylesheets');
	}

	/**
	 * [fetch description]
	 * @param  {[type]} $path
	 * @param  {[type]} $type
	 * @return {[type]}
	 */
	protected function fetch($path, $type, $override = false)
	{
		$manager = $this->manager();

		if (array_key_exists($path, $manager) && !$override) {
			return $manager[$path];
		}

		if ($type == 'javascripts') {
			$manager[$path] = $this->asset->javascripts($path);
		}

		if ($type == 'stylesheets') {
			$manager[$path] = $this->asset->stylesheets($path);
		}

		$this->manager($manager);

		return $manager[$path];
	}

	/**
	 * [manager description]
	 * @return {[type]}
	 */
	protected function manager($manager = null)
	{
 		if (!$this->cache->has('asset_pipeline_manager')) {
			$manager = array();
		}

		if (!is_null($manager)) {
			$this->cache->forever('asset_pipeline_manager', $manager);
		}

		return $this->cache->get('asset_pipeline_manager');
	}

	/**
	 * Gets the last updated time for the entire path
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	protected function lastUpdatedAt($path)
	{
		return filemtime($path);

		// $path = $this->getPath($path);
		// $this->checkDirectory($path);
		// $lastUpdatedAt = 0;
		// foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item)
		// {
		// 	$fileLastUpdatedAt = filemtime($item);
		// 	if ($fileLastUpdatedAt > $lastUpdatedAt) {
		// 		$lastUpdatedAt = $fileLastUpdatedAt;
		// 	}
		// }
		// return $lastUpdatedAt;		
	}
}
