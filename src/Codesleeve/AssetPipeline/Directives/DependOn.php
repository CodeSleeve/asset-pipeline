<?php

namespace Codesleeve\AssetPipeline\Directives;

class DependOn extends BaseDirective {

	public function process($directory)
	{
		throw new \InvalidArgumentException("Directive 'depend_on' has not yet been implemented");
	}

}