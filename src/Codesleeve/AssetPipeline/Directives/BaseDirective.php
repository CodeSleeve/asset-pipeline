<?php

namespace Codesleeve\AssetPipeline\Directives;

use Codesleeve\AssetPipeline\Exceptions\UnknownSprocketsDirective;

class BaseDirective extends \Codesleeve\AssetPipeline\SprocketsBase {

	protected $files = array();

	public function __construct($app, $manifestFile)
	{
		parent::__construct($app);
		$this->manifestFile = $manifestFile;
	}

	/**
	 * [isStylesheetManifest description]
	 * @return boolean [description]
	 */
	public function isStylesheetManifest()
	{
		return $this->getIncludePaths() == 'stylesheets';
	}

	/**
	 * [isJavascriptManifest description]
	 * @return boolean [description]
	 */
	public function isJavascriptManifest()
	{
		return $this->getIncludePaths() == 'javascripts';
	}

	/**
	 * This allows us to add our own files to a directive which
	 * then will override the files included via the directive itself
	 * 
	 * @param [type] $file [description]
	 */
	public function add($file)
	{
		$this->files[] = $file;
	}

	/**
	 * Let's us know if files have been added via event listeners
	 * 
	 * @return [type] [description]
	 */
	public function added($line)
	{
		$this->line = $line;
		$this->name = $this->getName();
		$this->param = $this->getParam();

		$this->event->fire('assets.register.directive', $this);

		return count($this->files) > 0;
	}

	/**
	 * See if a directive equals to string
	 * 
	 * @return string [description]
	 */
	public function equals($string)
	{
		return $this->line == $string;
	}

	/**
	 * Allows for custom directives if someone wants to register
	 * one... something like //= awesome_directive
	 * 
	 * @param  [type] $line [description]
	 * @return [type]       [description]
	 */
	public function process_basic($line)
	{
		if ($this->added($line)) {
			return $this->files;
		}

		throw new UnknownSprocketsDirective("Could not find the directive for $line");
	}

	/**
	 * When we do like require file, we want to filter certain paths for 
	 * javascripts and stylesheets, e.g. if we had smoke.css and smoke.js
	 * if we did not do this then smoke.js would show up in application.css
	 * when we do ...	*= require smoke ... in the application.css manifest
	 * 
	 * @return [type] [description]
	 */
	protected function getIncludePaths()
	{
		if (pathinfo($this->manifestFile, PATHINFO_EXTENSION) == 'js' || 
			strpos('.js', $this->manifestFile) !== false ||
			pathinfo($this->manifestFile, PATHINFO_EXTENSION) == 'coffee') {
			return 'javascripts';
		}

		else if (pathinfo($this->manifestFile, PATHINFO_EXTENSION) == 'css' ||
				 strpos('.css', $this->manifestFile) !== false ||
				 pathinfo($this->manifestFile, PATHINFO_EXTENSION) == 'less' ||
				 pathinfo($this->manifestFile, PATHINFO_EXTENSION) == 'scss') {
			return 'stylesheets';
		}

		return 'all';
	}

	/**
	 * Gets the name from $this->line
	 * @return [type] [description]
	 */
	protected function getName()
	{
		$name = explode(' ', $this->line);
		return ($name) ? $name[0] : 'unknown';
	}

	/**
	 * Gets the parameter from this->line
	 * @return [type] [description]
	 */
	protected function getParam()
	{
		$param = strstr($this->line, " ");
		$param = ($param) ? substr($param, 1) : $param;

		return $param;
	}

}