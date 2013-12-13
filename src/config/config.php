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
		'lib/assets/javascripts',
		'lib/assets/stylesheets',
		'provider/assets/javascripts',
		'provider/assets/stylesheets'
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
	*/
	'filters' => array(
		'.min.js' => array(

		),
		'.js' => array(
			new Codesleeve\AssetPipeline\Filters\MinifyJS(App::environment())
		),
		'.min.css' => array(
			new Codesleeve\AssetPipeline\Filters\URLRewrite
		),
		'.css' => array(
			new Codesleeve\AssetPipeline\Filters\URLRewrite,
			new Codesleeve\AssetPipeline\Filters\MinifyCSS(App::environment())
		),
		'.js.coffee' => array(
			new Codesleeve\AssetPipeline\Filters\CoffeeScript,
			new Codesleeve\AssetPipeline\Filters\MinifyJS(App::environment())
		),
		'.css.less' => array(
			new Assetic\Filter\LessphpFilter,
			new Codesleeve\AssetPipeline\Filters\URLRewrite,
			new Codesleeve\AssetPipeline\Filters\MinifyCSS(App::environment())
		),
		'.css.scss' => array(
			new Assetic\Filter\ScssphpFilter,
			new Codesleeve\AssetPipeline\Filters\URLRewrite,
			new Codesleeve\AssetPipeline\Filters\MinifyCSS(App::environment())
		),
		'.html' => array(
			new Codesleeve\AssetPipeline\Filters\JST,
			new Codesleeve\AssetPipeline\Filters\MinifyJS(App::environment())
		)
	),

	/*
	|--------------------------------------------------------------------------
	| mimes
	|--------------------------------------------------------------------------
	|
	| In order to know which mime type to send back to the server
	| we need to know if it is a javascript or stylesheet type. If
	| the extension is not found below then we just return a regular
	| download.
	|
	*/
	'mimes' => array(
	    'javascripts' => array('.js', '.js.coffee', '.min.js', '.html'),
	    'stylesheets' => array('.css', '.css.less', '.css.scss', '.min.css'),
	),

	/*
	|--------------------------------------------------------------------------
	| cache
	|--------------------------------------------------------------------------
	|
	| By default we cache all assets. This will greatly increase performance; however,
	| it is up to the developer to determine how the pipeline should tell Assetic to 
	| cache assets. You can create your own CacheInterface if the filesystem cache is
	| not up to your standards. See more in CacheInterface.php at
	|
	|    https://github.com/kriswallsmith/assetic/blob/master/src/Assetic/Cache
	|
	| If you want to turn off caching completely you can use this CacheInterface
	|
	|	'cache' => new Codesleeve\AssetPipeline\Filters\FilesNotCached,
	|
	*/
	'cache' => new Assetic\Cache\FilesystemCache(storage_path() . '/cache/asset-pipeline'),
	
	/*
	|--------------------------------------------------------------------------
	| concat
	|--------------------------------------------------------------------------
	|
	| This allows us to turn on the asset concatenation for specific
	| environments listed below. You can turn off local environment if
	| you are trying to troubleshoot, but you will likely have better
	| performance if you leave concat on (except if you are doing a lot
	| of minification stuff on each page refresh)
	|
	*/
	'concat' => array('production', 'local'),

	/*
	|--------------------------------------------------------------------------
	| directives
	|--------------------------------------------------------------------------
	|
	| This allows us to turn completely control which directives are used
	| for the sprockets parser that asset pipeline uses to parse manifest files.
	|
	| It is probably safe just to leave this alone unless you are familar with 
	| what is actually going on here.
	|
	*/
	'directives' => array(
		'require ' => new Codesleeve\Sprockets\Directives\RequireFile,
		'require_directory' => new Codesleeve\Sprockets\Directives\RequireDirectory,
		'require_tree' => new Codesleeve\Sprockets\Directives\RequireTree,
		'require_self' => new Codesleeve\Sprockets\Directives\RequireSelf,
	),

);
