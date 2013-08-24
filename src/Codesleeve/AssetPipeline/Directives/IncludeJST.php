<?php

namespace Codesleeve\AssetPipeline\Directives;

/**
 * Just an idea I had for a directive, but this really isn't needed
 * because I just include javascript templates anyway...
 * 
 * so I may get rid of this soon since it is non-rails-ish
 */
class IncludeJST extends BaseDirective {

	public function process()
	{
		return array($this->jstFile);
	}

}