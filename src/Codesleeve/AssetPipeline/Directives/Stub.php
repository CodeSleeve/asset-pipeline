<?php

namespace Codesleeve\AssetPipeline\Directives;

class Stub extends BaseDirective {

	public function process($directory)
	{
		throw new \InvalidArgumentException("Directive 'stub' has not yet been implemented");
	}

}