## What the heck is Asset Pipeline?

*_A video is coming soon._*

If you're looking to do the following in your Laravel 4 project, then spend 10 minutes reading over this.

 - Update your layouts to use the asset pipeline.
 - Place your less/css/javascript/coffeescript files into a folder.
 - Smile and let asset-pipeline automatically handle the compilation, concatenation, minification and caching.

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

Optionally you can put in the `Asset` facade under the `aliases` array in `app/config/app.php`. This is helpful if you want to use `Asset::htmls`, `Asset::javascripts`, or `Asset::stylesheets`

```php

     'Asset' => 'Codesleeve\AssetPipeline\Asset',
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

    <script src="<?= asset('assets/application/javascripts.js') ?>"></script>
or

    <?= HTML::script('assets/application/javascripts.js') ?>

A similar line of code for styles

    <link href="<?= asset('assets/application/stylesheets.css') ?>" rel="stylesheet" type="text/css">
or

    <?= HTML::style('assets/application/stylesheets.css') ?>


### Do I have to use `application/javascripts.js` though?

Basically any folder inside your assets directory can be routed too. (if you're curious about this then take a look at `vendor/codesleeve/asset-pipeline/src/routes.php`)

So say you don't want `/assets/application/javascripts.js` then you could create a directory `/assets/foobar` and place `file1.css` and `file1.js` inside that folder. 

Then point your browser to:

    http://<your laravel project>/assets/foobar.js

or for stylesheets

    http://<your laravel project>/assets/foobar.css


Nifty huh? This allows for you to control which assets are released if you choose to do so. But there's nothing wrong with
putting them all in `app/assets/application/javascripts/` and `app/assets/application/stylesheets` either.

### Html templating?

You could stick all your handlebar templates insde of the Laravel view but that adds up quickly so an alternative is to do,

    <?= Asset::htmls('application/templates') ?>

This brings in all the *.html found within the folder `app/assets/application/templates/`. This means your application can share all the templates with a single line of code. 

It is also possible to do something like 

    <?= Asset::htmls('application/templates/single-page-app-1') ?>
    <?= Asset::htmls('application/templates/single-page-app-2') ?>

If you want to have subdirectories for specific sections of your site. The `Asset::htmls` helper is recursive just like `Assets::javascripts` and `Assts::stylesheets` and will _*search 4 directories deep*_.

If you just want to link to a specific html page then be sure include the html extension, 

    <?= Asset::htmls('application/templates/somepage.html') ?>

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
	'directoryScan' => 10,	// minutes
```


### Forget

This is the ability to forget a cache on demand. Perhaps you just updated production and you don't want to wait on the directory scan to rebuild your cache then you can manually point your browser to any assets you may have with the forget parameter, e.g.

    http://<your laravel project>/assets/application/javascripts.js?forget=Ch4nG3m3!

This is assuming that you have the default configuration

```php
	'forget' => 'Ch4nG3m3!,
```

## FAQ

### What about conditional includes?

You can go about conditional includes in several ways. I'll start with how I do them

##### Conditionals #1

I put this html tag inside of my layout(s) which in turn, tells me what laravel action I am looking at.

    <html lang="en" class="<?= explode('@', Route::currentRouteAction())[0] ?> <?= explode('@', Route::currentRouteAction())[1] ?>">

And then inside of `application/stylesheets/home.index.less`

```css
	html.HomeController.index {
		body {
			background-color: #abcdef;
		}
	}
```

And if you ran `php artisan generate:assets` then you will find a file `app/assets/application/javascripts/!vendors/jquery.bootstrap.js` that allows you to run

```php
	$.bootstrap('html.HomeController.index', function(element) {
	   console.log('this code runs only when this element exists');
	});
```

##### Conditionals #2

Another alternative is to put your code inside of the specific Laravel view file. Assuming you included the `Asset` facade then inside of `app/views/home/index.php` you would have,

    <script><?= Asset::javascripts('partials/home.index.js') ?></script>
    <style type="text/css"><?= Asset::stylesheets('partials/home.index.less') ?><style>

This would spit out the file `app/assets/partials/home.index.js` right there into your script tags. It would also spit out the file `app/assets/partials/home.index.less`.

##### Conditionals #3

Another alternative is to just link directly to the partial asset. Inside of `app/views/home/index.php`

    <script src="<?= asset('assets/partials/home.index.js') ?>"></script>
    <link  href="<?= asset('assets/partials/home.index.css') ?>" rel="stylesheet" type="text/css">
    
Like I said before, I favor the first option. However, I don't know that any way better or worse than the other. One thing to keep in mind is that you won't be able to do less or coffeescript files if you link directly to the download (option #3).

Something else that is cool about option #1 is that you have 2 files (1 .js and 1 .css) that never change based on what page you are on, so it makes it super easy to cache all your assets with external 3rd-party software.

A downside to using option #1 is that all your assets are in 1 file so it might be difficult to troubleshoot bugs and errors - even when minify is turned off.

### How does caching work?

All script and stylesheet files are cached and only updated when a file in the directory changes. On production we only check to see if files have been updated every 10 minutes or whatever you set for the directoryScan configuration option.

## Support

Please file an issue if you see a problem. And enjoy!
