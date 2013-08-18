<?php
namespace Codesleeve\AssetPipeline;

class AssetFilters
{
	/**
	 * [__construct description]
	 * @param [type] $app
	 */
	public function __construct($app)
	{
		$this->env = $app['env'];
		$this->config = $app['config'];
		$this->event = $app['event'];
		$this->paths = $this->config->get('asset-pipeline::paths');	

		$this->filters = $this->config->get('asset-pipeline::filters');
	}

	public function extensions()
	{
		return array_keys($this->filters);
	}

	/**
	 * Gets the files filters that should be applied based
	 * on our configuration file.
	 * 
	 * @param  [type] $filepath [description]
	 * @return [type]           [description]
	 */
	public function matching($filepath)
	{
		$this->registerAllFilters();

		$filters = array();
		$extension = $this->hasValidExtension($filepath);
		$allFilters = $this->filters;

		if ($extension) {
			$filters = $allFilters[$extension];
		}

		return $filters;
	}

	/**
	 * [hasValidExtension description]
	 * @param  [type]  $file       [description]
	 * @param  [type]  $extensions [description]
	 * @return boolean             [description]
	 */
	public function hasValidExtension($filepath, $extensions = array())
	{
        $extensions = ($extensions) ? $extensions : $this->extensions();
		foreach($extensions as $extension) {
			if (stripos(strrev($filepath), strrev($extension)) === 0) {
				return $extension;
			}
		}
		return false;
	}

	/**
	 * Calls an event fire and adds filters to here from all of those
	 * 
	 * @return [type]
	 */
	private function registerAllFilters()
	{
		$this->event->fire('asset.register.filter', $this->filters);
	}
}