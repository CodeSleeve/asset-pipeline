<?php

namespace Codesleeve\AssetPipeline\Directives;

class BaseDirective extends \Codesleeve\AssetPipeline\SprocketsBase {

	public function __construct($app, $manifestFile)
	{
		parent::__construct($app);
		$this->manifestFile = $manifestFile;
		$this->paths = $app['config']->get('asset-pipeline::paths');
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
		return $this->getIncludePathType($this->manifestFile);
	}
}