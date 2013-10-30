<?php

namespace Codesleeve\AssetPipeline;

use Codesleeve\AssetPipeline\Filters\Impl\JavascriptsTypeFilter;
use Codesleeve\AssetPipeline\Filters\Impl\StylesheetsTypeFilter;
use Codesleeve\AssetPipeline\Filters\Impl\OthersTypeFilter;

final class FileTypeFilterProvider
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
                $this->types [AssetFilters::JAVASCRIPTS]
            ),
            AssetFilters::STYLESHEETS => new StylesheetsTypeFilter(
                $this->types [AssetFilters::STYLESHEETS]
            ),
            AssetFilters::OTHERS =>  new OthersTypeFilter(
                $this->types [AssetFilters::OTHERS]
            ),
        );
    }
    
    /**
     * get the filter of a given type
     * 
     * @param string $type
     * @return Codesleeve\AssetPipeline\Filters\FileTypeFilter
     */
    public function getTypeFilter($type)
    {
        return $this->filters[$type];
    }
}
