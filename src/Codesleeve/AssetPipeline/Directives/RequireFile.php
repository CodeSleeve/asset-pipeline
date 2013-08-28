<?php

namespace Codesleeve\AssetPipeline\Directives;

class RequireFile extends BaseDirective {

	public function process($filename)
	{
		if ($this->added("require $filename")) {
			return $this->files;
		}
		
		$file = $this->getRelativePath($filename, $this->getIncludePaths());
		return array($file);
	}

}