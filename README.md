## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `codesleeve/asset-pipeline`.

It might look something like:

```php
  "require": {
  	"laravel/framework": "4.0.*",
  	"codesleeve/asset-pipeline": "dev-master"
  }
```

Next, update Composer from the Terminal:

```php
    composer update
```

Once this operation completes, the final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

```php
    'Codesleeve\AssetPipeline\AssetPipelineServiceProvider'
```

We need to make sure your environment is setup correctly because the Asset Pipeline caches assets differently on a production environment than the development environment. 

If you're unsure what to put here for your machine name then do a `dd(gethostname());` to find out.

Inside `bootstrap/start.php`

```php
  $env = $app->detectEnvironment(array(
    'local' => array('your-machine-name'),
  ));
```

One more thing. Run the `artisan` command from the Terminal for the `generate:assets` command.

```php
    php artisan generate:assets
```

## Usage

Drop in a few javascript files and css files (or even coffee or less files). There is are some examples provided (assuming you ran the `generate:assets` command) that should help you get started.

Next you will need to point your browser to the correct location in order to get these assets.

Try going to:

    http://<your laravel project>/assets/application/javascripts.js
    
Or for styles:

    http://<your laravel project>/assets/application/stylesheets.js


Modify your layout view (or any view) to have something similar to this in it

```php
  <script src="<?= asset('assets/application/javascripts.js') ?>"></script>
```

And a similar line of code for styles

```php
  <link href="<?= asset('assets/application/stylesheets.css') ?>" rel="stylesheet" type="text/css">
```

### Do I have to use `application/javascripts.js` though?

Basically any folder inside your assets directory can be routed too. (if you're curious about this then take a look at `vendor/codesleeve/asset-pipeline/src/routes.php`)

So say you don't want `/assets/application/javascripts.js` then you could create a directory `/assets/foobar` and place `file1.css` and `file1.js` inside that folder. 

Then point your browser to:

    http://<your laravel project>/assets/foobar.js

or for stylesheets

    http://<your laravel project>/assets/foobar.css


Nifty huh? This allows for you to control which assets are released if you choose to do so. But there's nothing wrong with
putting them all in `app/assets/application/javascripts/` and `app/assets/application/stylesheets` either.

### Images? Fonts? Other files?

One thing you might be wondering is, how do I get my font-awesome fonts or my images? Assuming you do relative paths 
in your css (which any good designer should) then place your fonts and images inside of the `app/assets/font` and `app/assets/img` folders.

Font awesome just looks for '../font/<files>' so this works out nicely (the same applies for twitter bootstrap). Also a default structure is all laid out for you when you run `php artisan generate:assets`

You can access any file in your `app/assets` folder by going directly to the url, e.g.

    http://<your laravel project>/assets/font/FontAwesome.otf

This also means we can use route filters to protect our assets. If that tickles your fancy, then see [filtering](#filtering) section below.



## Conventions

  - Files can be nested 4 folders deep.
  - Files in subdirectories are loaded before files in parent directories.
  - Files are loaded in alphabetical order (assuming they are in the same directory).
  - Files need to have extensions .js, .coffee, .less, or .css to be included
  
## Configuration

If you would like to configure the asset pipeline there are a few things you can change.

First run the command:

```php
  php artisan config:publish codesleeve/asset-pipeline
```

This will create a config file in your `app/config/packages/` directory. You can edit this config file without having to worry about composer overriding your changes (if you were to edit the config file found in the package).

Next open the config found in `app/config/packages/codesleeve/asset-pipeline/config.php` and you will see the following values.

### Routing 

If you want to point your application somewhere besides `http://<laravel project dir>/assets/javascripts.js` you can change this

```php
	'routing' => array(
		'prefix' => '/assets'
	),
```

#### Filtering

You can place any kind of assets (fonts, images, mp3s) inside of the `app/assets` directory. This opens up the oppertunity for using Laravel route filters for your assets.

For example, say you don't want guest users looking at your assets, then you can add this to the config file.

```php
	'routing' => array(
		'prefix' => '/assets',
		'before' => 'auth',
	),
```

The auth filter comes in Laravel 4 by default in the `filters.php` file but I'll put it here just in case you wanted to see it.

```php
	Route::filter('auth', function()
	{
		if (Auth::guest()) return Redirect::guest('login');
	});
```

Simple huh?

### Path

This path is relative to your laravel root base directory `base_path()`

```php
  'path' => 'app/assets',
```

### Minify

If you want to turn off minification (for debugging perhaps?) you can do that easily here.

```php
  'minify' => true,
```

### Compressed

We won't compress files that are already compressed.

You might ask why not? We are using a php-min compressor (no YUI jar files around these parts!). However, there isn't
an option (that I could find) to turn off obscurify.

So, for example, if you use handlebars.js then you will likely get an error when that file is compressed because 
(from what I can tell) when that handlebars.js is obscurified it just flat out breaks. So a work around for this is 
to go ahead and compress handlebars.js yourself using <http://refresh-sf.com/yui/> and make sure 
`Minify only, no symbol obfuscation.` is checked.

```php
'compressed' => [
		'.min.', 
		'-min.'
	],
```

### Ignores

Next up is the `ignores` config. This allows us to ignore certain files or directories if we choose. By default we ignore the most common name for test folders.

```php
	'ignores' => [
		'/test/',
		'/tests/'
	],
```

### Directory Scan

In the asset pipeline we scan the assets directory to see if any files have changed and if so, we rebuild our Cache. Since we may not want to be constantly scanning our directory (file operations are expensive) we can wait a number of minutes before we look at the assets folder for any changes.

```php
	'ignores' => [
		'/test/',
		'/tests/'
	],
```


### Forget

This is the ability to forget a cache on demand. Perhaps you just updated production and you don't want to wait on the directory scan to rebuild your cache then you can manually point your browser to any assets you may have with the forget parameter, e.g.

    http://<your laravel project>/assets/application/javascripts.js?forget=Ch4nG3m3!

This is assuming that you have the default configuration

```php
	'forget' => 'Ch4nG3m3!,
```



## Support

Please file an issue if you see a problem. And enjoy!
