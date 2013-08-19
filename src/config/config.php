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
	| NOTE: Be sure not to remove .js or .css even though they are empty arrays.
	*/
	'filters' => array(
		'.js' => array(

		),
		'.css' => array(

		),
		'.js.coffee' => array(
			new Codesleeve\AssetPipeline\Filters\CoffeeScriptFilter
		),
		'.css.less' => array(
			new Assetic\Filter\LessphpFilter
		),
		'.css.scss' => array(
			new Assetic\Filter\ScssphpFilter
		),
		'.html' => array(
			new Codesleeve\AssetPipeline\Filters\JSTFilter
		)
	),

	/*
	|--------------------------------------------------------------------------
	| minify
	|--------------------------------------------------------------------------
	|
	| This allows us to turn on/off minify if we choose to do so. This might be
	| used for debugging.
	|
	| When minify is set to null, we simply ignore it and use the laravel environment
	| to determine if we should minify. The pipeline will minify when the environment
	| is set to "production". However, if minify is true or false then it overrides the
	| setting, regardless of what the environment is set to.
	|
	| NOTE that the minification filter will be ran automatically for us, 
	| we don't have to specify it here (it kicks in when the environment
	| is set to production assuming 'minify' is null)
	|
	*/
	'minify' => null

);