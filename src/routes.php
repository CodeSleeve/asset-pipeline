<?php

/**
 * This allows us to route to the correct assets
 */
Route::group(Config::get('assetPipeline::routing'), function() {
	Route::get('/{path}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');
});

// $jsDirectory = str_replace(':package_path', $this->base_path, Config::get('platform::assets.path'));
// $jsDirectory .= '/javascripts/*';

// $jsFilters = [];
// $coffeeFilters = [ new CoffeeScriptPhpFilter ];

// if (Config::get('platform::assets.minify'))
// {
// 	$jsFilters[] = new JSMinPlusFilter;
// 	$coffeeFilters[] = new JSMinPlusFilter;
// }

// $javascripts = new AssetCollection([
//     new GlobAsset($jsDirectory . '/*/*/*.js', $jsFilters),
//     new GlobAsset($jsDirectory . '/*/*/*.coffee', $coffeeFilters),

//     new GlobAsset($jsDirectory . '/*/*.js', $jsFilters),
//     new GlobAsset($jsDirectory . '/*/*.coffee',  $coffeeFilters),

//     new GlobAsset($jsDirectory . '/*.js', $jsFilters),
//     new GlobAsset($jsDirectory . '/*.coffee',  $coffeeFilters),

//     new GlobAsset($jsDirectory . '.js', $jsFilters),
//     new GlobAsset($jsDirectory . '.coffee',  $coffeeFilters),
// ]);

// $response = Response::make($javascripts->dump(), 200);

// $response->header('Content-Type', 'application/javascript');

// return $response;