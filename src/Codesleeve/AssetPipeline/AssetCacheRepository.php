<?php
namespace Codesleeve\AssetPipeline;

/**
 * We are using the following cache keys in Laravel
 *
 *   asset_pipeline_manager
 *   asset_pipeline_cached
 *
 * So if we ever to clear the cache, we just need to remove
 * these keys above...
 * 
 */

class AssetCacheRepository
{
	/**
	 * [__construct description]
	 * @param [type] $app
	 */
	public function __construct($app)
	{
		$this->env = $app['env'];
		$this->cache = $app['cache'];
		$this->config = $app['config'];
		$this->asset = $app['asset'];
	}

	/**
	 * If we should not cache then this just gets sent straight to asset pipeline
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
		if (!$this->shouldCache()) {
			return $this->asset->javascripts($path);			
		}

		return $this->fetch($path, 'javascripts');
	}

	/**
	 * If we should not cache this just gets sent straight to asset pipeline
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
		if (!$this->shouldCache()) {
			return $this->asset->stylesheets($path);
		}

		return $this->fetch($path, 'stylesheets');
	}

	/**
	 * Fetch returns the asset content for the given path
	 * 
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
	 * Manages all of the cached assets
	 * 
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
	 * We don't want to read the laravel config file on 
	 * every GET /assets/<file> (especially in production)
	 * so we cache this boolean value
	 * 
	 * @return boolean should we cache these assets?
	 */
	protected function shouldCache()
	{
 		if (!$this->cache->has('asset_pipeline_cached'))
 		{
 			$cached = $this->config->get('asset-pipeline::cache');

			$this->cache->forever('asset_pipeline_cached', $cached);
		}

		$cached = $this->cache->get('asset_pipeline_cached');

		if (is_null($cached)) {
			$cached = ($this->env == 'production') ? true : false;
		}

		return $cached;
	}
}
