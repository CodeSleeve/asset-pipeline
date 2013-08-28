<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| routing array
	|--------------------------------------------------------------------------
	|
	| This is passed to the Route::group and allows us to group and filter the
	| routes for our package
	|
	*/
	'routing' => array(
		'prefix' => '/assets'
	),

	/*
	|--------------------------------------------------------------------------
	| paths
	|--------------------------------------------------------------------------
	|
	| These are the directories we search for files in. 
	|
	| NOTE that the '.' in require_tree . is relative to where the manifest file 
	| (i.e. app/assets/javascripts/application.js) is located
	|
	*/
	'paths' => array(
		'app/assets/fonts',
		'app/assets/images',
		'app/assets/javascripts',
		'app/assets/stylesheets',
		'lib/assets/fonts',
		'lib/assets/images',
		'lib/assets/javascripts',
		'lib/assets/stylesheets',
		'vendor/assets/fonts',
		'vendor/assets/images',
		'vendor/assets/javascripts',
		'vendor/assets/stylesheets'
	),

	/*
	|--------------------------------------------------------------------------
	| filters
	|--------------------------------------------------------------------------
	|
	| In order for a file to be included with sprockets, it needs to be listed 
	| here and we can also do any preprocessing on files with the extension if
	| we choose to.
	|
	| NOTE: if you want to turn minification on for specific Laravel environments
	|       you could do (the same applies for MinifyCSS)
	|			new Codesleeve\AssetPipeline\Filters\MinifyJS(array('production', 'staging'))
	*/
	'filters' => array(
		'.min.js' => array(
			// don't minify files with this extension
		),
		'.min.css' => array(
			// don't minify files with this extension
		),
		'.js' => array(
			new Codesleeve\AssetPipeline\Filters\MinifyJS('production')
		),
		'.css' => array(
			new Codesleeve\AssetPipeline\Filters\MinifyCSS('production')
		),
		'.js.coffee' => array(
			new Codesleeve\AssetPipeline\Filters\CoffeeScriptFilter,
			new Codesleeve\AssetPipeline\Filters\MinifyJS('production')
		),
		'.css.less' => array(
			new Assetic\Filter\LessphpFilter,
			new Codesleeve\AssetPipeline\Filters\MinifyCSS('production')
		),
		'.css.scss' => array(
			new Assetic\Filter\ScssphpFilter,
			new Codesleeve\AssetPipeline\Filters\MinifyCSS('production')
		),
		'.html' => array(
			new Codesleeve\AssetPipeline\Filters\JSTFilter,
			new Codesleeve\AssetPipeline\Filters\MinifyJS('production')
		)
	),

	/*
	|--------------------------------------------------------------------------
	| cache
	|--------------------------------------------------------------------------
	|
	| This allows us to turn on/off the asset cache if we choose to do so.
	|
	| When cache is set to true we will cache assets that are served. Caching 
	| is probably a good idea to turn on in your production environment as it
	| will dramatically improve speed. 
	|
	| NOTE: anytime you change cache config you need to run	
	|
	|		php artisan assets:clean
	|
	| Your system admins can use assets:clean anytime they 
	| update servers where there have been changes to assets
	|
	| When set to null, cache will be true whenever laravel environment is
	| set to 'production' but false otherwise
	|
	*/
	'cache' => null,

	/*
	|--------------------------------------------------------------------------
	| concat
	|--------------------------------------------------------------------------
	|
	| This allows us to turn on/off the asset concatenation
	|
	| When concat is set to false javascript_link_tag will just a bunch of
	| different script tags but if it is true we will just get 1 single 
	| manifest file that has all the javascript from all the required files
	|
	| When set to null, concat will be true whenever laravel environment is 
	| set to 'production' but false otherwise
	|
	*/
	'concat' => null

);