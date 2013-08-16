<?php

namespace Codesleeve\AssetPipeline\Directives;

class RequireSelf extends BaseDirective {

	public function process()
	{
		throw new \InvalidArgumentException("Directive 'require_self' has not yet been implemented");
	}

}