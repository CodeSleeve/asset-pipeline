<?php

namespace Codesleeve\AssetPipeline;

use Codesleeve\AssetPipeline\Filters\FilterTypeProvider;
use Codesleeve\AssetPipeline\Filters\Impl\JavascriptsTypeFilter;
use Codesleeve\AssetPipeline\Filters\Impl\StylesheetsTypeFilter;
use Codesleeve\AssetPipeline\Filters\Impl\OthersTypeFilter;

final class FileTypeFilterProvider implements FilterTypeProvider
{
	/**
	 * constructor
	 * 
	 * @param object $app application instance
	 */
	public function __construct($app)
	{
		$this->app = $app;
		$this->config = $app['config'];
		$this->types = $this->config->get('asset-pipeline::filtertypes');
		$this->filters = array(
			AssetFilters::JAVASCRIPTS => new JavascriptsTypeFilter(
                $this,
				$this->types [AssetFilters::JAVASCRIPTS]
			),
			AssetFilters::STYLESHEETS => new StylesheetsTypeFilter(
                $this,
				$this->types [AssetFilters::STYLESHEETS]
			),
			AssetFilters::OTHERS =>  new OthersTypeFilter(
                $this,
				$this->types [AssetFilters::OTHERS]
			),
		);
	}
	
	/**
	 * @inheritdoc
	 */
	public function getTypeFilter($type)
	{
		return $this->filters[$type];
	}
}
