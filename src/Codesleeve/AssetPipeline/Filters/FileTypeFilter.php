<?php

namespace Codesleeve\AssetPipeline\Filters;

interface FileTypeFilter
{
	/**
	 * check whether a file is of the requested type
	 * 
	 * @return boolean	  returns true if the file is of the requested type or false
	 */
	public function isOfType($file);
}
