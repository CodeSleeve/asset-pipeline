<?php

/**
 * This allows us to route to the correct assets
 */
Route::group(Config::get('assetPipeline::routing'), function() {
	Route::get('/{path}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');
});