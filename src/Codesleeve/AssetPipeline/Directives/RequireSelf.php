<?php

namespace Codesleeve\AssetPipeline\Directives;

class RequireSelf extends BaseDirective {

	public function process()
	{
		$manifest = basename($this->manifestFile);
		$file = $this->getRelativePath($manifest, $this->getIncludePaths());
		
		return array($file);
	}

}