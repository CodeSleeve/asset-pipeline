<?php

use Codesleeve\AssetPipeline\Filters\EnvironmentFilter;

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
		'lib/assets/images',
		'provider/assets/javascripts',
		'provider/assets/stylesheets',
		'provider/assets/images'
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
	    'javascripts' => array('.js', '.js.coffee', '.coffee', '.html', '.min.js'),
	    'stylesheets' => array('.css', '.css.less', '.css.scss', '.less', '.scss', '.min.css'),
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
		'.min.css' => array(
			new Codesleeve\AssetPipeline\Filters\URLRewrite(App::make('url')->to('/')),
		),
		'.js' => array(
			new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\JSMinPlusFilter, App::environment()),
		),
		'.js.coffee' => array(
			new Codesleeve\AssetPipeline\Filters\CoffeeScript,
			new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\JSMinPlusFilter, App::environment()),
		),
		'.coffee' => array(
			new Codesleeve\AssetPipeline\Filters\CoffeeScript,
			new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\JSMinPlusFilter, App::environment()),
		),
		'.css' => array(
			new Codesleeve\AssetPipeline\Filters\URLRewrite(App::make('url')->to('/')),
			new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
		),
		'.css.less' => array(
			new Assetic\Filter\LessphpFilter,
			new Codesleeve\AssetPipeline\Filters\URLRewrite(App::make('url')->to('/')),
			new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
		),
		'.css.scss' => array(
			new Assetic\Filter\ScssphpFilter,
			new Codesleeve\AssetPipeline\Filters\URLRewrite(App::make('url')->to('/')),
			new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
		),
		'.less' => array(
			new Assetic\Filter\LessphpFilter,
			new Codesleeve\AssetPipeline\Filters\URLRewrite(App::make('url')->to('/')),
			new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
		),
		'.scss' => array(
			new Assetic\Filter\ScssphpFilter,
			new Codesleeve\AssetPipeline\Filters\URLRewrite(App::make('url')->to('/')),
			new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
		),
		'.html' => array(
			new Codesleeve\AssetPipeline\Filters\JST,
			new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\JSMinPlusFilter, App::environment()),
		)
	),

	/*
	|--------------------------------------------------------------------------
	| cache
	|--------------------------------------------------------------------------
	|
	| By default we cache all assets on 'production' environment. This will greatly 
	| increase performance; ultimately though, it is up to the developer to determine
	| how the pipeline should tell Assetic to cache assets. You can create your 
	| own CacheInterface if the filesystem cache is not up to your standards.
	|
	| See more in CacheInterface.php at
	|
	|    https://github.com/kriswallsmith/assetic/blob/master/src/Assetic/Cache
	|
	| If you want to turn off caching completely you could use this CacheInterface
	|
	|	'cache' => new Codesleeve\AssetPipeline\Filters\FilesNotCached,
	|
	| Info about the out of the box CacheInterfaces we are using below
	|
	|    CacheEnvironmentFilter -> only cache when in_array(App::environment, 'production')
	|    ClientCacheFilter -> creates a 304 response header when the underlying cache is not dirty
	|    FilesystemCache -> creates a cached file in the directory given to the constructor (we use laravel's storage path)
	|
	*/
	'cache' => 	new Codesleeve\AssetPipeline\Filters\CacheEnvironmentFilter(
					new Codesleeve\AssetPipeline\Filters\ClientCacheFilter(
						new Assetic\Cache\FilesystemCache(App::make('path.storage') . '/cache/asset-pipeline')), App::environment()),

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

	/*
	|--------------------------------------------------------------------------
	| javascript_files
	|--------------------------------------------------------------------------
	|
	| This allows us to inject in absolute paths to files that we want to always
	| include. This is useful for packages primarily or if you have a need
	| to always include certain files.
	|
	| It is probably safe just to leave this alone unless you are familar with
	| what is actually going on here.
	|
	*/
	'javascript_files' => array(

	),

	/*
	|--------------------------------------------------------------------------
	| stylesheet_files
	|--------------------------------------------------------------------------
	|
	| This allows us to inject in absolute paths to files that we want to always
	| include. This is useful for packages primarily or if you have a need
	| to always include certain files.
	|
	| It is probably safe just to leave this alone unless you are familar with
	| what is actually going on here.
	|
	*/
	'stylesheet_files' => array(

	),

	/*
	|--------------------------------------------------------------------------
	| javascript_include_tag
	|--------------------------------------------------------------------------
	|
	| This allows us to completely control how the javascript_include_tag function
	| works for asset pipeline.
	|
	| It is probably safe just to leave this alone unless you are familar with
	| what is actually going on here.
	|
	*/
	'javascript_include_tag' => new Codesleeve\AssetPipeline\Composers\JavascriptComposer,

	/*
	|--------------------------------------------------------------------------
	| stylesheet_link_tag
	|--------------------------------------------------------------------------
	|
	| This allows us to completely control how the stylesheet_link_tag function
	| works for asset pipeline.
	|
	| It is probably safe just to leave this alone unless you are familar with
	| what is actually going on here.
	|
	*/
	'stylesheet_link_tag' => new Codesleeve\AssetPipeline\Composers\StylesheetComposer,

	/*
	|--------------------------------------------------------------------------
	| image_tag
	|--------------------------------------------------------------------------
	|
	| This allows us to completely control how the image_tag function
	| works for asset pipeline.
	|
	| It is probably safe just to leave this alone unless you are familar with
	| what is actually going on here.
	|
	*/
	'image_tag' => new Codesleeve\AssetPipeline\Composers\ImageComposer,

	/*
	|--------------------------------------------------------------------------
	| controller_action
	|--------------------------------------------------------------------------
	|
	| Asset pipeline will route all requests through the controller action
	| listed here. This allows us to completely control how the controller
	| should behave for incoming requests for assets.
	|
	| It is probably safe just to leave this alone unless you are familar with
	| what is actually going on here.
	|
	*/
	'controller_action' => '\Codesleeve\AssetPipeline\AssetPipelineController@file',

	/*
	|--------------------------------------------------------------------------
	| sprockets_filter
	|--------------------------------------------------------------------------
	|
	| When concatenation is turned on, when an asset is fetched from the sprockets
	| generator it is filtered through this filter class named below. This allows us
	| to modify the sprockets filter if we need to behave differently.
	|
	| It is probably safe just to leave this alone unless you are familar with
	| what is actually going on here.
	|
	*/
	'sprockets_filter' => '\Codesleeve\Sprockets\SprocketsFilter',

	/*
	|--------------------------------------------------------------------------
	| sprockets_filter
	|--------------------------------------------------------------------------
	|
	| When concatenation is turned on, assets are filtered via SprocketsFilter
	| and we can do global filters on the resulting dump file. This would be
	| useful if you wanted to apply a filter to all javascript or stylesheet files
	| like minification. Out of the box we don't have any filters here. Add at
	| your own risk. I don't put minification filters here because the minify
	| doesn't always work perfectly and can bjork your entire concatenated
	| javascript or stylesheet file if it messes up.
	|
	| It is probably safe just to leave this alone unless you are familar with
	| what is actually going on here.
	|
	*/
	'sprockets_filters' => array(
		'javascripts' => array(),
		'stylesheets' => array(),
	),

);
