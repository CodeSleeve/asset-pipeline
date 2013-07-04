<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Routing array
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
	| path
	|--------------------------------------------------------------------------
	|
	| Set to <laravel_project>/app/assets folder by default. This is the
	| directory we search for.
	|
	*/
	'path' => 'app/assets',

	/*
	|--------------------------------------------------------------------------
	| minify
	|--------------------------------------------------------------------------
	|
	| Controls the minification filter. This is useful to turn off when debugging
	|
	*/
	'minify' => true,

	/*
	|--------------------------------------------------------------------------
	| manifest
	|--------------------------------------------------------------------------
	|
	| This allows us to specify the loading order of files. By default files are
	| loaded in alphabetical order from the top directory down
	|
	*/	
	'manifest' => array(
		'javascripts' => array(
			'vendor/*',
			'*'
		),
		'stylesheets' => array(
			'vendor/*',
			'*'
		),
		'htmls' => array(
			'vendor/*',
			'*'
		)
	),

	/*
	|--------------------------------------------------------------------------
	| compressed
	|--------------------------------------------------------------------------
	|
	| Before we minify a file we make sure the filename doesn't contain the 
	| patterns seen below. No regex allowed here. If you need to ignore a
	| specific file (say handlebars.js) then just put the name in there. Or 
	| you can rename the file to handlebars.min.js and it will be ignored.
	|
	*/
	'compressed' => array(
		'.min.', 
		'-min.'
	),

	/*
	|--------------------------------------------------------------------------
	| ignores
	|--------------------------------------------------------------------------
	|
	| This allows us to specify any files that we don't want included in the
	| asset pipeline ever. If you have multiple files named foobar.js then you will
	| need to put a fully qualified path in there like /cool-foobars/foobar.js
	|
	*/
	'ignores' => array(
		'/test/',
		'/tests/',
		'.ignoreme'
	),

	/*
	|--------------------------------------------------------------------------
	| directoryScan
	|--------------------------------------------------------------------------
	|
	| ** on production environment only **
	| This controls how often we scan the directory relative to where our assets 
	| are. If we scan the assets directory and a file is found to have been changed 
	| based on greatest filemtime then we will rebuild our cache of those assets. 
	| We may not want to scan a directory of assets for changes everytime we hit 
	| the server so this only happens every <you pick below> minutes.
	| 
	| ** on development environment **
	| This value doesn't do anything because we just scan the directory 
	| everytime and rebuild the assets cache anytime a file has been added 
	| or changed.
	|
	| ** this is in minutes **
	|
	*/
	'directoryScan' => 10,

	/*
	|--------------------------------------------------------------------------
	| forget
	|--------------------------------------------------------------------------
	|
	| This allows us to pass in a forget parameter at anytime and forget
	| our cached resources. So if we go to:

	| 	http://<sitename>/assets/application/javascripts.js?forget=Ch4nG3M3!

	| it will rebuild the javascript cache on the server. This is useful if
	| for some reason you want to manually trigger the cache rebuild on your
	| production environment and can't wait for the 'directoryScan' to kick in.
	|
	*/
	'forget' => 'Ch4nG3M3!'
);