<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Routing array
	|--------------------------------------------------------------------------
	|
	| This is passed to the Route::group and allows us to group and filter the
	| routes for our package
	|
	*/
	'routing' => [
		'prefix' => '/assets'
	],

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
	| compressed
	|--------------------------------------------------------------------------
	|
	| Before we minify a file we make sure the filename doesn't contain the 
	| patterns seen below. No regex allowed here. If you need to ignore a
	| specific file (say handlebars.js) then just put the name in there. Or 
	| you can rename the file to handlebars.min.js and it will be ignored.
	|
	*/
	'compressed' => [
		'.min.', 
		'-min.'
	],

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
	'ignores' => [
		'/test/',
		'/tests/'
	],


];