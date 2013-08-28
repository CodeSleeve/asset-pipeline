<?php

namespace Codesleeve\AssetPipeline\Directives;

class RequireSelf extends BaseDirective {

	public function process()
	{
		if ($this->added("require_self")) {
			return $this->files;
		} 

		$manifest = basename($this->manifestFile);
		$file = $this->getRelativePath($manifest, $this->getIncludePaths());
		
		return array($file);
	}

}