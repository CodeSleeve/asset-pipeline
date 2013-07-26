<?php
namespace Codesleeve\AssetPipeline;

use Illuminate\Support\Facades\Facade;

class AssetCache extends Facade {
	protected static function getFacadeAccessor() { return 'asset-cache'; }
}