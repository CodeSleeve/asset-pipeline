<?php
namespace Codesleeve\AssetPipeline;

class AssetPaths
{
	/**
	 * [__construct description]
	 * @param [type] $app
	 */
	public function __construct($app)
	{
		$this->env = $app['env'];
		$this->config = $app['config'];
		$this->events = $app['events'];
		$this->paths = $this->config->get('asset-pipeline::paths');	
	}

	/**
	 * [get description]
	 * @param  [type] $includes
	 * @return [type]
	 */
	public function get($includes)
	{
		$this->registerAllPaths();

		if ($includes == 'all') {
			return $this->paths;
		}

		$paths = array();

		foreach ($this->paths as $key => $path)
		{
			if ($this->matchesIncludes($key, $path, $includes)) {
				$paths[] = $this->getPath($key, $path);
			}
		}

		return $paths;
	}

	/**
	 * [registerPaths description]
	 * @return [type]
	 */
	protected function registerAllPaths()
	{
		$this->events->fire('assets.register.paths', $this->paths);
	}

	/**
	 * [matchesIncludes description]
	 * @param  [type] $key
	 * @param  [type] $path
	 * @param  [type] $includes
	 * @return [type]
	 */
	private function matchesIncludes($key, $path, $includes)
	{
		$types = explode(',', $path);

		if (strpos($key, $includes) !== false) {
			return true;
		}
		
		foreach($types as $type)
		{
			if (strpos($type, $includes) !== false) {
				return true;
			}			
		}

		return false;
	}

	/**
	 * [getPath description]
	 * @param  [type] $key
	 * @param  [type] $path
	 * @return [type]
	 */
	private function getPath($key, $path)
	{
		if (!is_int($key)) {
			return str_replace('\\', '/', $key);
		}

		return str_replace('\\', '/', $path);
	}
}
