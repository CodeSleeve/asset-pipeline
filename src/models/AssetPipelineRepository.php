<?php

namespace Codesleeve\AssetPipeline;

use Codesleeve\AssetPipeline\AsseticCustomFilters\IgnoreFilesFilter;
use Codesleeve\AssetPipeline\AsseticCustomFilters\CoffeeScriptPhpFilter;
use Codesleeve\AssetPipeline\AsseticCustomFilters\JSMinPlusFilter;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessphpFilter;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\CssRewriteFilter;

class AssetPipelineRepository implements AssetPipelineInterface {

	/**
	 * Passes the base_path into our asset pipeline and also ensures that
	 * it is a valid directory.
	 * 
	 * @param [type] $package_dir [description]
	 */
	public function __construct($basePath, $config_closure)
	{
		$this->basePath = $basePath;
		$this->config_closure = $config_closure;

		$this->checkDirectory($this->basePath);
	}

	/**
	 * [javascripts description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function javascripts($path = 'javascripts')
	{
		$path = $this->basePath . '/' . $this->config('assetPipeline::path') . '/' . $this->protect($path);

		$this->checkDirectory($path);

		$scripts = $this->process_scripts($path);

		return $scripts->dump();
	}

	/**
	 * [stylesheets description]
	 * @param  string $path [description]
	 * @return [type]       [description]
	 */
	public function stylesheets($path = 'stylesheets')
	{
		$path = $this->basePath . '/' . $this->config('assetPipeline::path') . '/' . $this->protect($path);

		$this->checkDirectory($path);

		$styles = $this->process_styles($path);

		return $styles->dump();
	}

	/**
	 * [process_scripts description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function process_scripts($folder)
	{
		$jsFilters = [ new IgnoreFilesFilter($folder, $this->config('assetPipeline::ignores')) ];
		$coffeeFilters = [ new IgnoreFilesFilter($folder, $this->config('assetPipeline::ignores')), new CoffeeScriptPhpFilter ];

		if ($this->config('assetPipeline::minify'))
		{
			$jsFilters[] = new JSMinPlusFilter($folder, $this->config('assetPipeline::compressed'));
			$coffeeFilters[] = new JSMinPlusFilter($folder, $this->config('assetPipeline::compressed'));
		}

		$javascripts = new AssetCollection([
		    new GlobAsset("$folder/*/*/*/*.js", $jsFilters),
		    new GlobAsset("$folder/*/*/*/*.coffee", $coffeeFilters),

		    new GlobAsset("$folder/*/*/*.js", $jsFilters),
		    new GlobAsset("$folder/*/*/*.coffee", $coffeeFilters),

		    new GlobAsset("$folder/*/*.js", $jsFilters),
		    new GlobAsset("$folder/*/*.coffee",  $coffeeFilters),

		    new GlobAsset("$folder/*.js", $jsFilters),
		    new GlobAsset("$folder/*.coffee",  $coffeeFilters),
		]);

		return $javascripts;
	}

	/**
	 * [process_stylesheets description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function process_styles($folder)
	{
		$cssFilters = [ new IgnoreFilesFilter($folder, $this->config('assetPipeline::ignores')) ];
		$lessFilters = [ new IgnoreFilesFilter($folder, $this->config('assetPipeline::ignores')), new LessphpFilter ];

		if ($this->config('assetPipeline::minify'))
		{
			$cssFilters[] = new CssMinFilter;
			$lessFilters[] = new CssMinFilter;
		}

		$stylesheets = new AssetCollection([
		    new GlobAsset("$folder/*/*/*.css", $cssFilters),
		    new GlobAsset("$folder/*/*/*.less", $lessFilters),

		    new GlobAsset("$folder/*/*.css", $cssFilters),
		    new GlobAsset("$folder/*/*.less", $lessFilters),

		    new GlobAsset("$folder/*.css", $cssFilters),
		    new GlobAsset("$folder/*.less", $lessFilters),

		    new GlobAsset("$folder.css", $cssFilters),
		    new GlobAsset("$folder.less", $lessFilters),
		]);

		return $stylesheets;
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
			throw new \InvalidArgumentException("Folder $folder is not a valid directory!");
		}		
	}

	protected function config($path)
	{
		$config_closure = $this->config_closure;
		return $config_closure($path);
	}
}