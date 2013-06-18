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
		$path = $this->getPath($path);

		$this->checkDirectory($path);

		$styles = $this->process_styles($path);

		return $styles->dump();
	}

	public function html($path = 'templates')
	{
		$path = $this->getPath($path);

		$this->checkDirectory($path);

		$html = $this->process_html($path);

		return $html->dump();
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
		$cssFilters = array( new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores')) );
		$lessFilters = array( new IgnoreFilesFilter($folder, $this->config->get('asset-pipeline::ignores')), new LessphpFilter );

		if ($this->config->get('asset-pipeline::minify'))
		{
			$cssFilters[] = new CssMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));
			$lessFilters[] = new CssMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed'));
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
	 * [process_templates description]
	 * @param  [type] $folder [description]
	 * @return [type]         [description]
	 */
	protected function process_html($folder)
	{
		$htmlFilters = array( new HtmlMinPlusFilter($folder, $this->config->get('asset-pipeline::compressed')) );

		$html = new AssetCollection([
		    new GlobAsset("$folder/*/*/*.html", $htmlFilters),
		    new GlobAsset("$folder/*/*.html", $htmlFilters),
		    new GlobAsset("$folder/*.html", $htmlFilters),
		    new GlobAsset("$folder.html", $htmlFilters),
		]);

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
			throw new \InvalidArgumentException("Folder $folder is not a valid directory!");
		}		
	}
}