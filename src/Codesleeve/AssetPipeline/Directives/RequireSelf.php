<?php

namespace Codesleeve\AssetPipeline\Directives;

class RequireSelf extends BaseDirective {

	public function process()
	{
		$file = $this->getRelativePath($this->manifestFile, $this->getIncludePaths());
		return array($file);
	}

}