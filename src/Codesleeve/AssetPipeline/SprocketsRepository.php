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
		$files = array($this->getFullPath($path));

		if ($this->env == 'production') {
			$filters[] = new JSMinPlusFilter;
			$files = $this->directives->getFilesFrom($this->getFullPath($path));
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
		$files = array($this->getFullPath($path));

		if ($this->env == 'production') {
			$filters = new CssMinPlusFilter;
			$files = $this->directives->getFilesFrom($this->getFullPath($path));
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
			if ($extension == 'js' || $extension == 'coffee') {
				$filters = $this->getFiltersFor($file);
				$assets[] = new FileAsset($file, $filters);
			}
		}

		return $assets;
	}

	/**
	 * Wraps script assets in a FileAsset objects for Assetic
	 * to do a AssetCollection with
	 */
	protected function getStyleAssets($files)
	{
		$assets = array();

		foreach ($files as $file) {
			$extension = pathinfo($file, PATHINFO_EXTENSION);
			if ($extension == 'css' || $extension == 'less') {
				$filters = $this->getFiltersFor($file);
				$assets[] = new FileAsset($file, $filters);
			}
		}

		return $assets;
	}

}