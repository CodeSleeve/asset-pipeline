<?php

namespace Codesleeve\AssetPipeline\Directives;

class IncludeFile extends BaseDirective {

	public function process($directory)
	{
		throw new \InvalidArgumentException("Directive 'include' has not yet been implemented");
	}

}