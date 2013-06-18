<?php

/**
 * This allows us to route to the correct assets
 */
Route::group(Config::get('asset-pipeline::routing'), function() {

	Route::get('/{path1}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path1}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');

	Route::get('/{path1}/{path2}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path1}/{path2}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');

	Route::get('/{path1}/{path2}/{path3}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path1}/{path2}/{path3}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');

	Route::get('/{path1}/{path2}/{path3}/{path4}.js', '\Codesleeve\AssetPipeline\AssetPipelineController@javascript');
	Route::get('/{path1}/{path2}/{path3}/{path4}.css', '\Codesleeve\AssetPipeline\AssetPipelineController@css');

	// catch all for files
	Route::get('/{path1}', '\Codesleeve\AssetPipeline\AssetPipelineController@file');
	Route::get('/{path1}/{path2}', '\Codesleeve\AssetPipeline\AssetPipelineController@file');
	Route::get('/{path1}/{path2}/{path3}', '\Codesleeve\AssetPipeline\AssetPipelineController@file');
	Route::get('/{path1}/{path2}/{path3}/{path4}', '\Codesleeve\AssetPipeline\AssetPipelineController@file');

});