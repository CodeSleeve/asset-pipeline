<?php

namespace Codesleeve\AssetPipeline\Directives;

class BaseDirective extends \Codesleeve\AssetPipeline\SprocketsBase {

	public function __construct($app, $manifestFile)
	{
		parent::__construct($app);
		$this->manifestFile = $manifestFile;
		$this->paths = $app['config']->get('asset-pipeline::paths');
	}

}