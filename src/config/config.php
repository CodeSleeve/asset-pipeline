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
		'app/assets/javascripts',
		'app/assets/stylesheets',
		'app/assets/images',
		'lib/assets/javascripts',
		'lib/assets/stylesheets',
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
	| NOTE that the minification filter will be ran automatically
	| for us, we don't have to specify it here (it kicks in when the environment
	| is set to production.
	|
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
	*/
	'minify' => null,

	/*
	|--------------------------------------------------------------------------
	| cache (coming soon)
	|--------------------------------------------------------------------------
	|
	| ** ON PRODUCTION ENVIRONMENT ONLY **
	|
	| This controls how often we scan for changes in all of the asset directories.
	|
	| If upon a scan a file is found to have been changed then we will rebuild
	| our cache of those assets. We certainly do not want to scan a directory
	| of assets for changes everytime we hit the server so this only happens 
	| every <you pick below> minutes.
	|
	| ** HOW DO I REFRESH MY PRODUCTION CACHE THOUGH? **
	|
	| 	php artisan assets:clean
	|
	| NOTE THOUGH that this is slightly different from rails because n the next 
	| page served the assets will be re-cached for you automatically. So for those 
	| of you familar with rails, you don't have to do like a `assets:precompile`.
	|
	*/
	'cache' => 1440,

);