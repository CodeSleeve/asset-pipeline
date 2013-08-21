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
		$this->paths = array();
		$this->registered = false;
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
			return array_keys($this->paths);
		}

		$paths = array();

		foreach ($this->paths as $path => $types)
		{
			if (in_array($includes, $types)) {
				$paths[] = str_replace('\\', '/', $path);
			}
		}

		return $paths;
	}

	/**
	 * Adds the path to the paths...
	 * 
	 * @param [type] $path [description]
	 * @param string $type [description]
	 */
	public function add($path, $types)
	{
		if (!is_array($types)) {
			$types = $this->getTypes($path, $types);
		}

		$this->paths[$path] = $types;
	}

	/**
	 * Removes the path from the paths...
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public function remove($path)
	{
		if (!array_key_exists($path, $this->paths)) {
			return false;
		}

		unset($this->paths[$path]);
		return true;
	}

	/**
	 * Extracts the type of asset from the $key, $value pair
	 * If $key is integer then we just have something like
	 * 
	 * 	[0] => 'app/assets/javascripts'
	 *
	 * Else we have something like
	 *
	 *	['my/assets/javascripts'] => 'javascripts'
	 *	['my/awesome/assets'] => 'stylesheets,javascripts'
	 *
	 * @param  [type] $path  [description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	protected function getTypes($key, $value)
	{
		if (is_int($key)) {
			if (strpos($value, 'javascripts') !== false) {
				return 'javascripts';
			} else if (strpos($value, 'stylesheets') !== false) {
				return 'stylesheets';
			} else {
				return 'other';
			}
		}

		$types = explode(',', $value);

		foreach ($types as $index => $type) {
			$types[$index] = trim($type);
		}

		return $types;
	}

	/**
	 * Extracts the path of asset from the $key,$value pair
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	protected function getPath($key, $value)
	{
		if (is_int($key)) {
			return $value;
		}

		return $key;
	}

	/**
	 * We register all the paths from our asset pipeline config file
	 * and from any various event listeners. 
	 * 
	 * We only register paths once and stick all of them inside of
	 * $this->paths array. There is no need to do it more than once.
	 * 
	 * @return [type]
	 */
	protected function registerAllPaths()
	{
		if ($this->registered) {
			return;
		}

		$this->registered = true;

		$paths = $this->config->get('asset-pipeline::paths');

		foreach ($paths as $key => $value)
		{
			$path = $this->getPath($key, $value);
			$type = $this->getTypes($key, $value);
			$this->add($path, $type);
		}

		$this->events->fire('assets.register.paths', $this);
	}

}
