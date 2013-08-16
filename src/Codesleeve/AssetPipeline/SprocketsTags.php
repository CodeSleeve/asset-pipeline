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
		if ($this->env == 'production') {
			return $this->create_javascript_include($manifestFile, $attributes);
		}

		$files = $this->directives->getFilesFrom($this->getFullPath($manifestFile));

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
		if ($this->env == 'production') {
			return $this->create_stylesheet_link($manifestFile, $attributes);
		}

		$files = $this->directives->getFilesFrom($this->getFullPath($manifestFile));

		$tags = "";
		foreach ($files as $file) {
			$tags .= $this->create_stylesheet_link($file, $attributes) . PHP_EOL;
		}

		return $tags;		
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
	 * Creates a script tag given a file path
	 * 
	 * @param  [type] $filepath       [description]
	 * @param  [type] $attributes [description]
	 * @return [type]             [description]
	 */
	protected function create_javascript_include($filepath, $attributes)
	{
		$tag = '<script src="' . $this->getUrlPath($filepath) .'"';
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
		$tag = '<link href="' . $this->getUrlPath($filepath) . '"';
		foreach ($attributes as $attribute_key => $attribute_value) {
			$tag .= " $attribute_key=\"$attribute_value\"";
		}
		$tag .= '>';

		return $tag;		
	}

}