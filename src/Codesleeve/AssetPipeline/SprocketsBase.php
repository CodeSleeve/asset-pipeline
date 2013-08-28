<?php

namespace Codesleeve\AssetPipeline;

// use Codesleeve\AssetPipeline\AsseticCustomFilters\CoffeeScriptPhpFilter;
// use Assetic\Asset\FileAsset;
// use Assetic\Asset\GlobAsset;
// use Assetic\Filter\LessphpFilter;

class SprocketsBase {

	/**
	 * Passes the base_path into our asset pipeline and also ensures that
	 * it is a valid directory.
	 * 
	 * @param [type] $package_dir [description]
	 */
	public function __construct($app)
	{
		$this->app = $app;
		$this->basePath = $this->normalizePath($app['path.base']);
		$this->config = $app['config'];
		$this->env = $app['env'];
		$this->routingPrefix = $this->config->get('asset-pipeline::routing.prefix', '/assets') . '/';
		$this->jstFile = '_jst_.js';
		$this->event = $app['events'];

		$this->filters = new AssetFilters($app);
		$this->paths = new AssetPaths($app);
	}

	/**
	 * Returns the url to get to a given asset
	 * 
	 * @param  [type] $filepath   [description]
	 * @param  array  $extensions [description]
	 * @return [type]             [description]
	 */
	public function getUrlPath($filepath, $includes = 'all')
	{
		foreach($this->paths->get($includes) as $path)
		{
			if (substr($filepath, 0, strlen($path)) == $path) {
				$filepath = substr($filepath, strlen($path));
				continue;
			}
		}

		return $this->getAppUrlPath($this->routingPrefix . ltrim($filepath, '/'));
	}

	/**
	 * Gets the full file or directory path
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public function getFullPath($filepath, $includes = 'all')
	{
		$this->protect($filepath);

		$file = $this->getFullFile($filepath, $includes);

		if ($file) {
			return $file;
		}

		$directory = $this->getFullDirectory($filepath);
	
		if ($directory) {
			return $directory;
		}

		if ($filepath == $this->jstFile) {
			return $filepath;
		}

		throw new Exceptions\InvalidPath('Cannot find given path in paths: ' . $filepath);
	}

	/**
	 * Get the relative file or relative directory path
	 * 
	 * @param  [type] $filepath   [description]
	 * @param  array  $includes [description]
	 * @return [type]             [description]
	 */
	public function getRelativePath($filepath, $includes = 'all')
	{
		return str_replace($this->basePath . '/', '', $this->getFullPath($filepath, $includes));
	}

	/**
	 * Get the files within a folder
	 * 
	 * @param  [type] $dirpath [description]
	 * @return [type]          [description]
	 */
	public function getFilesInFolder($dirpath, $recursive = false, $includes = 'all')
	{
		$parent = $dirpath;
		$folder = $this->getFullDirectory($dirpath, $includes);
		$relativeFolder = $this->getRelativeDirectory($dirpath, $includes);

		$paths = array();
		$files = array();
		$directories = array();

		if ($handle = opendir($folder))
		{
		    while (false !== ($path = readdir($handle))) 
		    {
		    	$fullpath = $folder . '/' . $path;

		        if ($recursive && is_dir($fullpath) && $path != '.' && $path != '..') {
		        	$directories[] = $parent . '/' . $path;
		        } else if (is_file($fullpath) && $this->filters->hasValidExtension($fullpath)) {
		        	$files[] = $relativeFolder . '/' . $this->normalizePath($path);
		        }
		    }
			closedir($handle);
		}

		sort($files);
		sort($directories);

		foreach($directories as $directory) {
			$paths = array_merge($paths, $this->getFilesInFolder($directory, $recursive, $includes));
		}

		$paths = array_merge($paths, $files);

		return $paths;
	}

	/**
	 * Let's us know if we should concatenate files into one big giant file
	 * or not. It's up to the config really.
	 * 
	 * @return [type] [description]
	 */
	public function shouldConcat()
	{
		$concat = $this->config->get('asset-pipeline::concat');

		if (is_null($concat)) {
			$concat = ($this->env == "production") ? true : false;
		}

		return $concat;
	}

	/**
	 * Loops through all the paths and extensions and tries to find
	 *  this file we provided in the $filepath (i.e. find the first jquery.js)
	 * 
	 * @param  [type] $filepath   [description]
	 * @param  array  $extensions [description]
	 * @return [type]             [description]
	 */
	protected function getFullFile($filepath, $includes = 'all')
	{
		$filepath = $this->replaceRelativeDot($filepath);
		$extensions = array_merge(array(''), $this->filters->extensions());

		foreach ($this->paths->get($includes) as $path) {
			foreach ($extensions as $extension) {
				$file = $this->basePath . "/$path/$filepath$extension";
				if (is_file($file)) {
					return $this->normalizePath($file);
				}
			}
		}

		return null;
	}

	/**
	 * Try to find the first directory in the file paths we have
	 * in our config file
	 * 
	 * @param  [type] $filepath   [description]
	 * @param  array  $extensions [description]
	 * @return [type]             [description]
	 */
	protected function getFullDirectory($dirpath, $includes = 'all')
	{
		$dirpath = $this->replaceRelativeDot($dirpath);

		foreach ($this->paths->get($includes) as $path) {
			$dir = $this->basePath . "/$path/$dirpath";
			if (is_dir($dir)) {
				return $this->normalizePath(rtrim($dir, '/'));
			}
		}

		return null;
	}

	/**
	 * [getFullDirectory description]
	 * @param  [type] $filepath   [description]
	 * @return [type]             [description]
	 */
	protected function getRelativeDirectory($dirpath, $includes = 'all')
	{
		return str_replace($this->basePath . '/', '', $this->getFullDirectory($dirpath, $includes));
	}

	/**
	 * Strips off the paths from the beginning of string 
	 * if we find a path that is...
	 * 
	 * @param  [type] $filepath [description]
	 * @return [type]           [description]
	 */
	protected function basePath($filepath, $includes = 'all')
	{
		$filepath = str_replace($this->basePath . '/', '', $filepath);
		$filepath = $this->normalizePath($filepath);

		foreach ($this->paths->get($includes) as $path)
		{
			if (stripos($filepath, $path) === 0) {
				return ltrim(substr($filepath, strlen($path)), '/');
			}
		}

		return $filepath;
	}

	/**
	 * Replaces the . or ./ with just an empty string
	 * so we get a relative path feel
	 * 
	 * @param  [type] $filepath [description]
	 * @return [type]           [description]
	 */
	protected function replaceRelativeDot($filepath)
	{
		$filepath = $this->normalizePath($filepath);
		$filepath = preg_replace('/^\.\//', '', $filepath);
		$filepath = preg_replace('/^\./', '', $filepath);
		return $filepath;
	}

	/**
	 * [protect description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function protect($folder)
	{
		if (str_replace('..', '', $folder) !== $folder) {
			throw new Exceptions\InvalidPath('Cannot have .. in the path!');
		}
	}

	/**
	 * Lets us tap into laravel's helper function and get the asset wrapper
	 * for this path... in case someone is hosting at like 
	 * http://sitename/subpath/assets/ or something...
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	protected function getAppUrlPath($path)
	{
		$path = $this->normalizePath($path);

		if (isset($this->app['url']))
		{
			return app('url')->asset($path, $this->config->get('secure'));
		}
		
		return $path;
	}

	/**
	 * This is used to convert any Windows slashes into unix style slashes
	 *
	 * @param [type] $path [description]
	 * @return [type] 	   [description]
	 */
	protected function normalizePath($path)
	{
		return str_replace('\\', '/', $path);
	}
}