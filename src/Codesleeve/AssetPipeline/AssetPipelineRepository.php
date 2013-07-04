<?php

namespace Codesleeve\AssetPipeline;

use Codesleeve\AssetPipeline\AsseticCustomFilters\IgnoreFilesFilter;
use Codesleeve\AssetPipeline\AsseticCustomFilters\CoffeeScriptPhpFilter;
use Codesleeve\AssetPipeline\AsseticCustomFilters\JSMinPlusFilter;
use Codesleeve\AssetPipeline\AsseticCustomFilters\CssMinPlusFilter;
use Codesleeve\AssetPipeline\AsseticCustomFilters\HtmlMinPlusFilter;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessphpFilter;

class AssetPipelineRepository implements AssetPipelineInterface {

	/**
	 * Passes the base_path into our asset pipeline and also ensures that
	 * it is a valid directory.
	 * 
	 * @param [type] $package_dir [description]
	 */
	public function __construct($basePath, $config)
	{
		$this->basePath = $basePath;
		$this->config = $config;

		$this->checkDirectory($this->basePath);
	}

	/**
	 * [javascripts description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function javascripts($path = 'javascripts')
	{
		$path = $this->getPath($path);

		if (!is_file($path)) {
			$this->checkDirectory($path);
		}

		$filters = array();
		$filters[] = new IgnoreFilesFilter($path, $this->config->get('asset-pipeline::ignores'));

		if ($this->config->get('asset-pipeline::minify')) {
			$filters[] = new JSMinPlusFilter($path, $this->config->get('asset-pipeline::compressed'));
		}

		$scripts = new AssetCollection($this->getScriptAssets($path), $filters);

		return $scripts->dump();
	}

	/**
	 * [stylesheets description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function stylesheets($path = 'stylesheets')
	{
		$path = $this->getPath($path);

		if (!is_file($path)) {
			$this->checkDirectory($path);
		}

		$filters = array();
		$filters[] = new IgnoreFilesFilter($path, $this->config->get('asset-pipeline::ignores'));

		if ($this->config->get('asset-pipeline::minify')) {
			$filters[] = new CssMinPlusFilter($path, $this->config->get('asset-pipeline::compressed'));
		}

		$styles = new AssetCollection($this->getStyleAssets($path), $filters);

		return $styles->dump();
	}

	/**
	 * [htmls description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function htmls($path = 'templates')
	{
		$path = $this->getPath($path);

		if (!is_file($path)) {
			$this->checkDirectory($path);
		}

		$filters = array();
		$filters[] = new IgnoreFilesFilter($path, $this->config->get('asset-pipeline::ignores'));

		if ($this->config->get('asset-pipeline::minify')) {
			$filters[] = new HtmlMinPlusFilter($path, $this->config->get('asset-pipeline::compressed'));
		}

		$html = new AssetCollection($this->getHtmlAssets($path), $filters);

		return $html->dump();
	}

	/**
	 * [javascript description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function javascript($path = 'javascripts')
	{
		return $this->javascripts($path);
	}

	/**
	 * [stylesheet description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function stylesheet($path = 'stylesheet')
	{
		return $this->stylesheets($path);
	}

	/**
	 * [html description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function html($path = 'templates')
	{
		return $this->htmls($path);
	}

	/**
	 * Returns the last time any file was changed within this directory
	 * 
	 * @return [type] [description]
	 */
	public function lastUpdatedAt($path)
	{
		$path = $this->getPath($path);
		$this->checkDirectory($path);
		
		$lastUpdatedAt = 0;
		foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item)
		{
			$fileLastUpdatedAt = filemtime($item);

			if ($fileLastUpdatedAt > $lastUpdatedAt) {
				$lastUpdatedAt = $fileLastUpdatedAt;
			}
		}

		return $lastUpdatedAt;
	}

	/**
	 * Returns the base path of where all the assets are located
	 * 
	 * @return [type] [description]
	 */
	public function getPath($path)
	{
		return $this->normalizePath($this->basePath . '/' . $this->config->get('asset-pipeline::path') . '/' . $this->protect($path));
	}

	/**
	 * [getFiles description]
	 * @param  [type] $folder     [description]
	 * @param  array  $extensions [description]
	 * @return [type]             [description]
	 */
	public function getFiles($folder, $manifest, $extensions = array())
	{
		$files = array();
		$last_files = array();

		foreach ($manifest as $path)
		{
			$fullpath = $this->normalizePath("$folder/$path");

			if (is_file($fullpath))
			{
				$files[] = $fullpath;
			} 
			else
			{
				foreach (glob($fullpath) as $file)
				{
					if ($file == "." || $file == "..") {
						// ignore these files
					} else if (is_dir($file)) {
						$files = array_merge($files, $this->getFilesRecursively($file, $extensions));
					} else if (is_file($file) && $this->hasExtension($file, $extensions)) {
						if ($path == "*") {
							$last_files[] = $this->normalizePath($file);
						} else {
							$files[] = $this->normalizePath($file);
						}
					}
				}
			}
		}

		return array_unique(array_merge($files, $last_files));
	}

	/**
	 * [getScriptAssets description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function getScriptAssets($folder, $extensions = array('js', 'coffee'))
	{
		if (is_file($folder)) {
			return array($this->getScriptAsset($folder));
		}

		$assets = array();
		$manifest = $this->config->get('asset-pipeline::manifest.javascripts', array('*'));

		foreach ($this->getFiles($folder, $manifest, $extensions) as $file) {
			$asset = $this->getScriptAsset($file);
			if ($asset) $assets[] = $asset;
		}

		return $assets;
	}

	/**
	 * [getScriptAsset description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	protected function getScriptAsset($file)
	{
		$filters = array();
		$extension = pathinfo($file, PATHINFO_EXTENSION);

		if ($extension == 'coffee') {
			$filters[] = new CoffeeScriptPhpFilter;
		}

		return ($extension == 'js' || $extension == 'coffee') ? new FileAsset($file, $filters) : null;
	}

	/**
	 * [getStyleAssets description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function getStyleAssets($folder, $extensions = array('css', 'less'))
	{
		if (is_file($folder)) {
			return array($this->getStyleAsset($folder));
		}

		$assets = array();
		$manifest = $this->config->get('asset-pipeline::manifest.stylesheets', array('*'));

		foreach ($this->getFiles($folder, $manifest, $extensions) as $file) {
			$asset = $this->getStyleAsset($file);
			if ($asset) $assets[] = $asset;
		}

		return $assets;
	}

	/**
	 * [getStyleAsset description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	protected function getStyleAsset($file)
	{
		$filters = array();
		$extension = pathinfo($file, PATHINFO_EXTENSION);

		if ($extension == 'less') {
			$filters[] = new LessphpFilter;
		}

		return ($extension == 'css' || $extension == 'less') ? new FileAsset($file, $filters) : null;
	}

	/**
	 * [getStyleAssets description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function getHtmlAssets($folder, $extensions = array('html'))
	{
		if (is_file($folder)) {
			return array($this->getHtmlAsset($folder));
		}

		$assets = array();
		$manifest = $this->config->get('asset-pipeline::manifest.htmls', array('*'));

		foreach ($this->getFiles($folder, $manifest, $extensions) as $file) {
			$asset = $this->getHtmlAsset($file);
			if ($asset) $assets[] = $asset;
		}

		return $assets;
	}

	/**
	 * [getFilesRecursively description]
	 * @param  [type] $folder     [description]
	 * @param  array  $extensions [description]
	 * @return [type]             [description]
	 */
	protected function getFilesRecursively($folder, $extensions = array())
	{
		$directory_files = array();
		$files = array();

		if ($handle = opendir($folder))
		{
		    while (false !== ($entry = readdir($handle))) 
		    {
		    	$fullEntry = $folder . DIRECTORY_SEPARATOR . $entry;
		        if ($entry == "." || $entry == "..") {
		            
		        } else if (is_dir($fullEntry)) {
		        	$directory_files = array_merge($directory_files, $this->getFilesRecursively($fullEntry, $extensions));
		        } else if (is_file($fullEntry)) {
		        	$files[] = $fullEntry;
		        }
		    }
		    closedir($handle);
		}

		return array_merge($directory_files, $files);
	}

	/**
	 * [getHtmlAsset description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	protected function getHtmlAsset($file)
	{
		return new FileAsset($file);
	}

	/**
	 * [protect description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function protect($folder)
	{
		return str_replace('../', '', $folder);
	}

	/**
	 * [normalizePath description]
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	protected function normalizePath($path)
	{
		return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
	}

	/**
	 * [hasExtension description]
	 * @param  [type]  $file      [description]
	 * @param  [type]  $extension [description]
	 * @return boolean            [description]
	 */
	protected function hasExtension($file, $extensions)
	{
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        return in_array($extension, $extensions);
	}

	/**
	 * [checkDirectory description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function checkDirectory($folder) 
	{
		if (!is_dir($folder)) {
			throw new \InvalidArgumentException("$folder is not a valid directory or file!");
		}		
	}

}