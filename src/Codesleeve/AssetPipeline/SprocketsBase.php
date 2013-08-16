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
		$this->basePath = $app['path.base'];
		$this->config = $app['config'];
		$this->env = $app['env'];
		$this->paths = $this->config->get('asset-pipeline::paths');	
		$this->routingPrefix = $this->config->get('asset-pipeline::routing.prefix') . '/';
		$this->filters = $this->config->get('asset-pipeline::filters');
		$this->extensions = array_keys($this->filters);
		$this->jstFile = '_jst_.js';
	}

	/**
	 * Returns the url to get to a given asset
	 * 
	 * @param  [type] $filepath   [description]
	 * @param  array  $extensions [description]
	 * @return [type]             [description]
	 */
	public function getUrlPath($filepath, $extensions = array(''))
	{
		foreach($this->paths as $path)
		{
			if (substr($filepath, 0, strlen($path)) == $path) {
				$filepath = substr($filepath, strlen($path));
				continue;
			}
		}

		return $this->routingPrefix . ltrim($filepath, DIRECTORY_SEPARATOR);
	}

	/**
	 * Gets the full file or directory path
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public function getFullPath($filepath, $extensions = array(''))
	{
		$this->protect($filepath);
		$extensions = array_merge($extensions, $this->extensions);

		$file = $this->getFullFile($filepath, $extensions);

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
	 * @param  array  $extensions [description]
	 * @return [type]             [description]
	 */
	public function getRelativePath($filepath, $extensions = array(''))
	{
		return str_replace($this->app['path.base'] . '/', '', $this->getFullPath($filepath, $extensions));
	}

	/**
	 * Get the files within a folder
	 * 
	 * @param  [type] $dirpath [description]
	 * @return [type]          [description]
	 */
	public function getFilesInFolder($dirpath, $recursive = false)
	{
		$parent = $dirpath;
		$folder = $this->getFullDirectory($dirpath);
		$relativeFolder = $this->getRelativeDirectory($dirpath);

		$paths = array();
		$files = array();
		$directories = array();

		if ($handle = opendir($folder))
		{
		    while (false !== ($path = readdir($handle))) 
		    {
		    	$fullpath = $folder . DIRECTORY_SEPARATOR . $path;

		        if ($recursive && is_dir($fullpath) && $path != '.' && $path != '..') {
		        	$directories[] = $parent . DIRECTORY_SEPARATOR . $path;
		        } else if (is_file($fullpath) && $this->hasValidExtension($fullpath)) {
		        	$files[] = $relativeFolder . '/' . $path;
		        }
		    }
			closedir($handle);
		}

		sort($files);
		sort($directories);

		foreach($directories as $directory) {
			$paths = array_merge($paths, $this->getFilesInFolder($directory, $recursive));
		}

		$paths = array_merge($paths, $files);

		return $paths;
	}

	/**
	 * Loops through all the paths and extensions and tries to find
	 *  this file we provided in the $filepath (i.e. find the first jquery.js)
	 * 
	 * @param  [type] $filepath   [description]
	 * @param  array  $extensions [description]
	 * @return [type]             [description]
	 */
	protected function getFullFile($filepath, $extensions = array(''))
	{
		$dirpath = $this->replaceRelativeDot($filepath);

		foreach ($this->paths as $path) {
			foreach ($extensions as $extension) {
				$file = $this->app['path.base'] . "/$path/$filepath$extension";
				if (is_file($file)) {
					return $file;
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
	protected function getFullDirectory($dirpath)
	{
		$dirpath = $this->replaceRelativeDot($dirpath);

		foreach ($this->paths as $path) {
			$dir = $this->app['path.base'] . "/$path/$dirpath";
			if (is_dir($dir)) {
				return rtrim($dir, DIRECTORY_SEPARATOR);
			}
		}

		return null;
	}

	/**
	 * [getFullDirectory description]
	 * @param  [type] $filepath   [description]
	 * @param  array  $extensions [description]
	 * @return [type]             [description]
	 */
	protected function getRelativeDirectory($dirpath)
	{
		return str_replace($this->app['path.base'] . "/", '', $this->getFullDirectory($dirpath));
	}

	/**
	 * [getRelativeFile description]
	 * @param  [type] $filepath [description]
	 * @return [type]           [description]
	 */
	protected function getRelativeFile($filepath, $extensions)
	{
		return str_replace($this->app['path.base'] . '/', '', $this->getFullPath($filepath, $extensions));
	}

	/**
	 * Strips off the paths from the beginning of string 
	 * if we find a path that is...
	 * 
	 * @param  [type] $filepath [description]
	 * @return [type]           [description]
	 */
	protected function basePath($filepath)
	{
		foreach ($this->paths as $path)
		{
			if (stripos($filepath, $path) === 0) {
				return ltrim(substr($filepath, strlen($path)), DIRECTORY_SEPARATOR);
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
		$filepath = preg_replace('/^\.\//', '', $filepath);
		$filepath = preg_replace('/^\./', '', $filepath);
		return $filepath;
	}

	/**
	 * [hasValidExtension description]
	 * @param  [type]  $file       [description]
	 * @param  [type]  $extensions [description]
	 * @return boolean             [description]
	 */
	protected function hasValidExtension($filepath, $extensions = array())
	{
        $extensions = ($extensions) ? $extensions : array_keys($this->config->get('asset-pipeline::filters'));
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
	protected function getFiltersFor($filepath)
	{
		$filters = array();
		$extension = $this->hasValidExtension($filepath);
		$allFilters = $this->config->get("asset-pipeline::filters");

		if ($extension) {
			$filters = $allFilters[$extension];
		}

		return $filters;
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
}