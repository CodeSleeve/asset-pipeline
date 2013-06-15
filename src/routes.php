<?php

/**
 * This allows us to route to the correct assets
 */
Route::group(Config::get('assetPipeline::routing'), function() {
	Route::get('/{path}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');

	Route::get('/{path1}/{path2}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path1}/{path2}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');

	Route::get('/{path1}/{path2}/{path3}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path1}/{path2}/{path3}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');

	Route::get('/{path1}/{path2}/{path3}/{path4}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path1}/{path2}/{path3}/{path4}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');
});