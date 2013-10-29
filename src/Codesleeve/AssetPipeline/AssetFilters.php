<?php
namespace Codesleeve\AssetPipeline;

class AssetFilters
{
    /**
     * filter categories
     */
    const JAVASCRIPTS = 'javascripts';
    const STYLESHEETS = 'stylesheets';
    const OTHERS = 'others';
    
	/**
	 * [__construct description]
	 * @param [type] $app
	 */
	public function __construct($app)
	{
		$this->app = $app;
		$this->env = $app['env'];
		$this->config = $app['config'];
		$this->events = $app['events'];
		$this->filters = $this->config->get('asset-pipeline::filters');
		$this->registered = false;
	}

	/**
	 * Adds an extension for you with the following filters
	 * 
	 * @param [type] $extension [description]
	 * @param [type] $filters   [description]
	 */
	public function add($extension, $filters)
	{
		if (!is_array($filters)) {
			$filters = array($filters);
		}

		$this->filters[$extension] = $filters;
	}

	/**
	 * [extensions description]
     * @param  [type] $category     [description]
	 * @return [type]               [description]
	 */
	public function extensions($category = null)
	{
		$this->registerAllFilters();
        if (is_null($category)) {
            return array_keys($this->filters);
        }
        return $this->getExtensionsOfCategory($category);
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

		if (is_array($filters))
		{
			foreach ($filters as $filter)
			{
				if (method_exists($filter, 'initialize')) {
					$filter->initialize($this->app);
				}
			}			
		}

		return $filters;
	}

	/**
	 * Removes an extension for you
	 * 
	 * @param  [type] $extension [description]
	 * @return [type]            [description]
	 */
	public function remove($extension)
	{
		if (!array_key_exists($extension, $this->filters)) {
			return false;
		}

		unset($this->filters[$extension]);
	}

	/**
	 * Calls an event fire and adds filters to here from all of those
	 * 
	 * @return [type]
	 */
	private function registerAllFilters()
	{
		if ($this->registered) {
			return;
		}

		$this->registered = true;
		$this->events->fire('assets.register.filters', $this);
	}
    
    /**
     * get filter extensions of a given category
     * 
     * @param  string       $category     requested filter category
     * @return array        returns the filter exensions
     */
    private function getExtensionsOfCategory($category)
    {
        $class = 'Codesleeve\\AssetPipeline\\Filters\\Impl\\'.
            ucfirst($category).'TypeFilter';
        $filter = new $class();
        return array_values(
            array_filter(array_keys($this->filters), array($filter, 'isOfType'))
        );
    }
}
