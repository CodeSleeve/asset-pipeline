<?php

namespace Codesleeve\AssetPipeline\Filters\Impl;

use Codesleeve\AssetPipeline\AssetFilters;
use Codesleeve\AssetPipeline\FileTypeFilterProvider;
use Codesleeve\AssetPipeline\Filters\AbstractFileTypeFilter;

final class OthersTypeFilter extends AbstractFileTypeFilter
{
	/**
	 * @inheritdoc
	 */
	protected function overrideableIsOfType($file)
	{
		$scriptfilter = $this->provider->getTypeFilter(AssetFilters::JAVASCRIPTS);
		$stylefilter = $this->provider->getTypeFilter(AssetFilters::STYLESHEETS);
		return !$scriptfilter->isOfType($file) && !$stylefilter->isOfType($file);
	}
}
