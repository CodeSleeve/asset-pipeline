<?php

namespace Codesleeve\AssetPipeline\Filters;

interface FilterTypeProvider
{
	/**
	 * get the filter of a given type
	 * 
	 * @param string $type
	 * @return Codesleeve\AssetPipeline\Filters\FileTypeFilter
	 */
	public function getTypeFilter($type);
}
