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

		if (is_file($path)) {
			return $this->process_script($path)->dump();
		}

		$this->checkDirectory($path);

		$scripts = $this->process_scripts($path);

		return $scripts->dump();
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
	 * [stylesheets description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function stylesheets($path = 'stylesheets')
	{
		$path = $this->getPath($path);

		if (is_file($path)) {
			return $this->process_style($path)->dump();
		}

		$this->checkDirectory($path);

		$styles = $this->process_styles($path);

		return $styles->dump();
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
	 * [htmls description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function htmls($path = 'templates')
	{
		$path = $this->getPath($path);

		if (is_file($path)) {
			return $this->process_html($path)->dump();
		}

		$this->checkDirectory($path);

		$html = $this->process_htmls($path);

		return $html->dump();
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
		return $this->basePath . '/' . $this->config->get('asset-pipeline::path') . '/' . $this->protect($path);
	}

	/**
	 * [process_scripts description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function process_scripts($folder)
	{
		$jsFilters = array( new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores')) );
		$coffeeFilters = array( new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores')), new CoffeeScriptPhpFilter );

		if ($this->config->get('asset-pipeline::minify'))
		{
			$jsFilters[] = new JSMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));
			$coffeeFilters[] = new JSMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));
		}
	
		$javascripts = new AssetCollection(array(
		    new GlobAsset("$folder/*/*/*/*.js", $jsFilters),
		    new GlobAsset("$folder/*/*/*/*.coffee", $coffeeFilters),

		    new GlobAsset("$folder/*/*/*.js", $jsFilters),
		    new GlobAsset("$folder/*/*/*.coffee", $coffeeFilters),

		    new GlobAsset("$folder/*/*.js", $jsFilters),
		    new GlobAsset("$folder/*/*.coffee",  $coffeeFilters),

		    new GlobAsset("$folder/*.js", $jsFilters),
		    new GlobAsset("$folder/*.coffee",  $coffeeFilters),
		));

		return $javascripts;
	}

	/**
	 * [process_script description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	protected function process_script($file)
	{
		$folder = pathinfo($file)['dirname'];
		$extension = pathinfo($file)['extension'];

		$filters = array();
		$assetFile = array();
		$filters[] = new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores'));

		if ($extension == 'coffee') {
			$filters[] = new CoffeeScriptPhpFilter;
		}

		if ($this->config->get('asset-pipeline::minify')) {
			$filters[] = new JSMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));
		}

		if ($extension == 'js' || $extension == 'coffee') {
			$assetFile[] = new FileAsset($file, $filters);
		}

		$javascripts = new AssetCollection($assetFile);

		return $javascripts;
	}

	/**
	 * [process_stylesheets description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function process_styles($folder)
	{
		$cssFilters = array( new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores')) );
		$lessFilters = array( new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores')), new LessphpFilter );

		if ($this->config->get('asset-pipeline::minify')) {
			$cssFilters[] = new CssMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));
			$lessFilters[] = new CssMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));
		}

		$stylesheets = new AssetCollection(array(
		    new GlobAsset("$folder/*/*/*/*.css", $cssFilters),
		    new GlobAsset("$folder/*/*/*/*.less", $lessFilters),

		    new GlobAsset("$folder/*/*/*.css", $cssFilters),
		    new GlobAsset("$folder/*/*/*.less", $lessFilters),

		    new GlobAsset("$folder/*/*.css", $cssFilters),
		    new GlobAsset("$folder/*/*.less", $lessFilters),

		    new GlobAsset("$folder/*.css", $cssFilters),
		    new GlobAsset("$folder/*.less", $lessFilters),
		));

		return $stylesheets;
	}

	/**
	 * [process_style description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	protected function process_style($file)
	{
		$folder = pathinfo($file)['dirname'];
		$extension = pathinfo($file)['extension'];

		$assetFile = array();
		$filters = array();
 		$filters[] = new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores'));

 		if ($extension == 'less') {
 			$filters[] = new LessphpFilter;
 		}

		if ($this->config->get('asset-pipeline::minify')) {
			$filters[] = new CssMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));
		}

		if ($extension == 'css' || $extension == 'less') {
			$assetFile[] = new FileAsset($file, $filters);
		}

		$stylesheets = new AssetCollection($assetFile);

		return $stylesheets;
	}

	/**
	 * [process_templates description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function process_htmls($folder)
	{
		$htmlFilters = array();
		$htmlFilters[] = new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores'));
		$htmlFilters[] = new HtmlMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));

		$html = new AssetCollection(array(
		    new GlobAsset("$folder/*/*/*/*.html", $htmlFilters),
		    new GlobAsset("$folder/*/*/*.html", $htmlFilters),
		    new GlobAsset("$folder/*/*.html", $htmlFilters),
		    new GlobAsset("$folder/*.html", $htmlFilters),
		));

		return $html;
	}

	/**
	 * [process_html description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	protected function process_html($file)
	{
		$folder = pathinfo($file)['dirname'];
		$extension = pathinfo($file)['extension'];

		$assetFile = array();
		$filters = array();
		$filters[] = new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores'));
		$filters[] = new HtmlMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));

		if ($extension == 'html') {
			$assetFile[] = new FileAsset($file, $filters);
		}

		$html = new AssetCollection($assetFile);

		return $html;
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