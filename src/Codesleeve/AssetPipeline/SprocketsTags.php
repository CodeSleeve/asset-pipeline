<?php

namespace Codesleeve\AssetPipeline;

/**
 * Gives us our include tags for javascript and css
 */
class SprocketsTags extends SprocketsBase {

	/**
	 * Passes the base_path into our asset pipeline and also ensures that
	 * it is a valid directory.
	 * 
	 * @param [type] $package_dir [description]
	 */
	public function __construct($app)
	{
		parent::__construct($app);
		$this->directives = new SprocketsDirectives($app);

	}

	/**
	 * Returns a list of javascript tags for given manifest file
	 * 
	 * @param  string $manifestFile [description]
	 * @param  array  $attributes   [description]
	 * @return [type]               [description]
	 */
	public function javascriptIncludeTag($manifestFile = 'application', $attributes = array())
	{
		if ($this->shouldConcat()) {
			return $this->create_javascript_include($manifestFile . '.js', $attributes);
		}

		$files = $this->directives->getFilesFrom($this->getFullPath($manifestFile, 'javascripts'));

		$tags = "";
		foreach ($files as $file) {
			$tags .= $this->create_javascript_include($file, $attributes) . PHP_EOL;
		}

		return $tags;
	}

	/**
	 * Returns a list of stylesheet tags for given manifest file
	 * 
	 * @param  string $manifestFile       [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function stylesheetLinkTag($manifestFile = 'application', $attributes = array())
	{
		if ($this->shouldConcat()) {
			return $this->create_stylesheet_link($manifestFile . '.css', $attributes);
		}

		$files = $this->directives->getFilesFrom($this->getFullPath($manifestFile, 'stylesheets'));

		$tags = "";
		foreach ($files as $file) {
			$tags .= $this->create_stylesheet_link($file, $attributes) . PHP_EOL;
		}

		return $tags;		
	}

	/**
	 * Returns the url to the image
	 * 
	 * @param  [type] $file       [description]
	 * @param  [type] $attributes [description]
	 * @return [type]             [description]
	 */
	public function imageTag($file, $attributes = array())
	{
		$tag = '<img src="' . $this->getUrlPath($file) . '"';
		foreach ($attributes as $attribute_key => $attribute_value) {
			$tag .= " $attribute_key=\"$attribute_value\"";
		}
		$tag .= '>';

		return $tag;		
	}

	/**
	 * Offering a snake case way to call this method
	 * (since rails does it this way)
	 * 
	 * @param  string $manifestFile       [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function javascript_include_tag($manifestFile = 'application', $attributes = array())
	{
		return $this->javascriptIncludeTag($manifestFile, $attributes);
	}

	/**
	 * Offering a snake case way to call this method
	 * (since rails does it this way)
	 * 
	 * @param  string $manifestFile       [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function stylesheet_link_tag($manifestFile = 'application', $attributes = array())
	{
		return $this->stylesheetLinkTag($manifestFile, $attributes);
	}

	/**
	 * Offering a snake case way to call this method
	 * 
	 * @param  [type] $file       [description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function image_tag($file, $attributes = array())
	{
		return $this->imageTag($file, $attributes);
	}

	/**
	 * Creates a script tag given a file path
	 * 
	 * @param  [type] $filepath       [description]
	 * @param  [type] $attributes [description]
	 * @return [type]             [description]
	 */
	protected function create_javascript_include($filepath, $attributes)
	{
		$tag = '<script src="' . $this->getUrlPath($filepath, 'javascripts') .'"';
		foreach ($attributes as $attribute_key => $attribute_value) {
			$tag .= " $attribute_key=\"$attribute_value\"";
		}
		$tag .= '></script>';

		return $tag;
	}

	/**
	 * Creates a style link given a file path
	 * 
	 * @param  [type] $filepath   [description]
	 * @param  [type] $attributes [description]
	 * @return [type]             [description]
	 */
	protected function create_stylesheet_link($filepath, $attributes)
	{
		$tag = '<link rel="stylesheet" type="text/css" href="' . $this->getUrlPath($filepath, 'stylesheets') . '"';
		foreach ($attributes as $attribute_key => $attribute_value) {
			$tag .= " $attribute_key=\"$attribute_value\"";
		}
		$tag .= '>';

		return $tag;		
	}

}