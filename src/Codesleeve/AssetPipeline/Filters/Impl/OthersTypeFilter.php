<?php

namespace Codesleeve\AssetPipeline\Filters\Impl;

use Codesleeve\AssetPipeline\FileTypeFilterProvider;
use Codesleeve\AssetPipeline\Filters\AbstractFileTypeFilter;

final class OthersTypeFilter extends AbstractFileTypeFilter
{
	/**
	 * @inheritdoc
	 */
	protected function overrideableIsOfType($file)
	{
		$scriptfilter = new JavascriptsTypeFilter();
		$stylefilter = new StylesheetsTypeFilter();
		return !$scriptfilter->isOfType($file) && !$stylefilter->isOfType($file);
	}
}
