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
	 * If we should concat then this should probably be a 
	 * manifest file so we should process directives inside
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public function javascripts($path)
	{
		$filters = array();
		$files = array($this->getFullPath($path, 'javascripts'));

		if ($this->shouldConcat()) {
			$files = $this->directives->getFilesFrom($this->getFullPath($path, 'javascripts'));
		}

		$scripts = new AssetCollection($this->getScriptAssets($files), $filters);

		return $scripts->dump();
	}

	/**
	 * Dumps out the stylesheets for this file
	 * 
	 * If we should concat then this should probably be a 
	 * manifest file so we should process directives inside
	 * 
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	public function stylesheets($path)
	{
		$filters = array();
		$files = array($this->getFullPath($path, 'stylesheets'));

		if ($this->shouldConcat()) {
			$files = $this->directives->getFilesFrom($this->getFullPath($path, 'stylesheets'));
		}

		$styles = new AssetCollection($this->getStyleAssets($files), $filters);
		return $styles->dump();
	}

	/**
	 * Tests to see if this file in the $path exists or not 
	 * as a javascript file
	 * 
	 * @param  [type]  $path
	 * @return boolean
	 */
	public function isJavascript($path)
	{
		try {
			$file = $this->getFullPath($path, 'javascripts');
			return $this->filters->hasValidExtension($path) !== false;
		} catch (\Exception $e) {

		}

		return false;
	}

	/**
	 * Tests to see if this file in the $path exists or not
	 * as a stylesheet file
	 * 
	 * @param  [type]  $path
	 * @return boolean
	 */
	public function isStylesheet($path)
	{
		try {
			$file = $this->getFullPath($path, 'stylesheets');
			return $this->filters->hasValidExtension($path) !== false;
		} catch (\Exception $e) {
			
		}

		return false;
	}

	/**
	 * Wraps script assets in a FileAsset objects for Assetic
	 * to do a AssetCollection with
	 */
	protected function getScriptAssets($files)
	{
		$assets = array();

		foreach ($files as $file)
		{	
			$base = $this->basePath($file);
			if ($this->isJavascript($base))
			{
				$filters = $this->filters->matching($file);
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

		foreach ($files as $file)
		{
			$base = $this->basePath($file);
			if ($this->isStylesheet($base))
			{
				$filters = $this->filters->matching($base);
				$assets[] = new FileAsset($this->getFullPath($base, 'stylesheets'), $filters);
			}
		}

		return $assets;
	}

}