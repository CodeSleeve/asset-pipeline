<?php

namespace Codesleeve\AssetPipeline;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Codesleeve\AssetPipeline\Filters\JSMinPlusFilter;
use Codesleeve\AssetPipeline\Filters\CssMinPlusFilter;

/**
 * The purpose of this class is to parse out javascript and css
 * using Assetic libraries. It also extends SprocketTags for the
 * ability to do stuff like javascript_include_tag. Lastly, there 
 * are some helper functions in SprocketsBase for resolving 
 * urls/paths/etc
 * 
 */
class SprocketsRepository extends SprocketsTags {

	/**
	 * Dumps out the javascript for this file
	 * 
	 * If we are in production this should probably be a 
	 * manifest file so we should process directives inside
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public function javascripts($path)
	{
		$filters = array();
		$files = array($this->getFullPath($path, 'javascripts'));

		$minify = $this->config->get('asset-pipeline::minify');

		if ($minify === true) {
			$filters[] = new JSMinPlusFilter;			
		}

		if ($this->env == 'production') {
			$files = $this->directives->getFilesFrom($this->getFullPath($path, 'javascripts'));
			if (is_null($minify)) {
				$filters[] = new JSMinPlusFilter;
			}
		}

		$scripts = new AssetCollection($this->getScriptAssets($files), $filters);

		return $scripts->dump();
	}

	/**
	 * Dumps out the stylesheets for this file
	 * 
	 * If we are in production this should probably be a 
	 * manifest file so we should process directives inside
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public function stylesheets($path)
	{
		$filters = array();
		$files = array($this->getFullPath($path, 'stylesheets'));

		$minify = $this->config->get('asset-pipeline::minify');

		if ($minify === true) {
			$filters[] = new CssMinPlusFilter;
		}

		if ($this->env == 'production') {
			$files = $this->directives->getFilesFrom($this->getFullPath($path, 'stylesheets'));
			if (is_null($minify)) {
				$filters[] = new CssMinPlusFilter;
			}
		}

		$styles = new AssetCollection($this->getStyleAssets($files), $filters);
		return $styles->dump();
	}

	/**
	 * Wraps script assets in a FileAsset objects for Assetic
	 * to do a AssetCollection with
	 */
	protected function getScriptAssets($files)
	{
		$assets = array();

		foreach ($files as $file) {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			
			if ($file != '_jst_.js' && ($extension == 'js' || $extension == 'coffee' || $extension == 'html')) {
				$filters = $this->getFiltersFor($file);
				$base = $this->basePath($file);
				$assets[] = new FileAsset($this->getFullPath($base, 'javascripts'), $filters);
			}
		}

		return $assets;
	}

	/**
	 * Wraps stylesheet assets in a FileAsset objects for
	 * Assetic to do a AssetCollection with
	 */
	protected function getStyleAssets($files)
	{
		$assets = array();

		foreach ($files as $file) {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			if ($extension == 'css' || $extension == 'less' || $extension == 'scss') {
				$filters = $this->getFiltersFor($file);
				$base = $this->basePath($file);				
				$assets[] = new FileAsset($this->getFullPath($base, 'stylesheets'), $filters);
			}
		}

		return $assets;
	}

	/**
	 * Creates a file asset that is basically just
	 * a jst created from an html file
	 * 
	 * @return [type] [description]
	 */
	protected function getTemplateAssets()
	{
		$assets = array();
		$files = $this->getFilesInFolder('.', true);
		$filters = $this->getFiltersFor('.html');

		foreach ($files as $file) {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			if ($extension == 'html') {
				$base = $this->basePath($file);
				$assets[] = new FileAsset($this->getFullPath($base, 'javascripts'), $filters);
			}
		}

		return $assets;
	}
}