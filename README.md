## What the heck is Asset Pipeline?

*_A video is coming soon._*

For those of you familar with Rails asset pipeline and sprockets, you will hopefully feel right at home using this package.

In a nutshell, this is what you will get from this package.

 - Update your layouts to use the asset pipeline.
 - Put your less/css/javascript/coffeescript/jst/image/font/etc files into app/assets
 - Smile and let asset-pipeline automatically handle the compilation, concatenation, minification and even caching.

## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `codesleeve/asset-pipeline`.

It might look something like:

```php
  "require": {
  	"laravel/framework": "4.0.*",
  	"codesleeve/asset-pipeline": "4.0.*"
  }
```

Next, update Composer from the Terminal:

```php
    composer update
```

Once this operation completes, add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

```php
    'Codesleeve\AssetPipeline\AssetPipelineServiceProvider'
```

Also, we need to make sure your environment is setup correctly because the asset pipeline caches and minfies assets on a production environment.

Inside `bootstrap/start.php`

```php
  $env = $app->detectEnvironment(array(
    'local' => array('your-machine-name'),
  ));
```

One more thing. Run the `artisan` command from the Terminal for the `generate:assets` command.

```php
    php artisan assets:generate
```

## Usage

After running `php artisan assets:generate` you should notice two new directories with some files, `app/assets` and `vendor/assets`.

Be sure to check out the `app/assets/javascripts/application.js` and `app/assets/stylesheets/application.css` files, where you can adjust the files you want included into your manifest.

Let's verify you have everything working by going to:

    http://<your laravel project>/assets/application.js
    
Or for styles:

    http://<your laravel project>/assets/application.css

Now to bring everything in, (this is exactly how rails does it too) you should add to your layout

    <?= stylesheet_link_tag() ?>
    <?= javascript_include_tag() ?>

### Javascript Templates?

You could stick all your handlebar templates insde of the Laravel view but that adds up quickly. Any .html files you place within `app/assets/javascripts` will be accessible in a global javascript variable called JST. Just open up your javascript console and examine the `JST` object.

### Images? Fonts? Other files?

Place your fonts and images inside of the `app/assets/fonts` and `app/assets/images` folders. For you rails fans, can create image tags in your view using  

```php
  <?= image_tag('filename.png', ['alt' => 'The alt for the image', 'class' => 'img-responsive']) ?>
```

## Configuration

If you would like to configure the asset pipeline there are a few things you can change.

First run the command:

```php
  php artisan config:publish codesleeve/asset-pipeline
```

This will create a config file in your `app/config/packages/` directory. You can edit this config file without having to worry about composer overriding your changes.

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

### Paths
These are the directories we search for files in relative to your laravel root base directory `base_path()`

```php
  'paths' => array(
    'app/assets/javascripts',
    'app/assets/stylesheets',
    'app/assets/images',
    'lib/assets/javascripts',
    'lib/assets/stylesheets',
    'vendor/assets/javascripts',
    'vendor/assets/stylesheets'
  ),
```

How is this used? Say you search for `/assets/foobar.js`, the asset pipeline will search for (in order) any path that has 'javascripts' in it.

So you might finally find '/assets/foobar.js` inside of `vendor/assets/javascripts/foobar.js`

But what if you had a path that did not have the string 'javascripts' in it? You can use the word 'javascripts' to tell asset pipeline that
the resources in this directory are javascripts. The same applies for stylesheets.

  'paths' => array(
    ... code omitted ...,
    'app/some/other/directory' => 'javascripts',
    'app/directory/with/style' => 'stylesheets'
  ),


### Filters

These filters are what determine 
  1) if we should consider the file in a manifest and 
  2) how to filter files of this extension type.

```php
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
```

You can even add your own, if you want to write your own filter or even change existing filters out. For example, I've not found a decent sass compiler in php, but if I do found one, all I would have to do is add:

```php
  'filters' => array(

    ... code omited ...

    '.css.sass' => array(
      new My\Awesome\SassFilter
    )
  ),
```

### Minify

If you want to turn on/off minification (for debugging perhaps?) you can do that easily here.
Leaving the value as null will fallback to the machine's environment to determine if assets are minified.

```php
  'minify' => null,
```

## Cache

In the asset pipeline, there is no configuration item for this but on the back end we are caching elements when the laravel environment detects itself as being in production.

To clear the cache you can run

```php
  php artisan assets:clean
```

## FAQ

### What about conditional includes?

You can go about conditional includes in several ways. Let me show you how I do them

I put this html tag inside of my layout(s) which in turn, tells me what laravel action I am looking at.

    <html lang="en" class="<?= $currentRoute ?>">

And in my `app/controllers/BaseController.php` I might have something like

```php

  protected function setupLayout()
  {
    if ( ! is_null($this->layout))
    {
      $this->layout = View::make($this->layout);
      View::share('currentRoute', $this->currentRoute());
    }
  }

  protected function currentRoute()
  {
    $controller = explode('@', Route::currentRouteAction())[0];
    $controller = strtolower(str_replace('Controller', '', $controller));
    $action = strtolower(explode('@', Route::currentRouteAction())[1]);

    return "$controller $action";
  }
```

And then inside of `app/assets/stylesheets/login.css.less`, assuming the route action was something like: `UsersController@login`

```css
	html.users.login {
		body {
			background-color: #abcdef;
		}
	}
```

If you ran `php artisan assets:generate` then you will find a file `vendor/assets/javascripts/jquery.bootstrap.js` that allows you to run

```php
	$.bootstrap('html.users.login', function(element) {
	   console.log('this code runs only when this element exists');
	});
```

However, I rarely find myself doing this since I can start the code in the laravel view or use Marionette's App.start();

### How does caching work?

All script and stylesheet files are cached only in production mode. The cache will be built on the first time it is requested from the server. It says alive forever, until the server admin runs a `php artisan assets:clean` to clear the cache. It is using Laravels' Cache facade, which uses the `file` driver out of the box, but you
can make it use `memory` or `redis` or whatever you fancy.

### This asset pipeline is totally different?

Yes, you might want to change your composer.json to use the alpha version via `"codesleeve/asset-pipeline": "alpha"'` if you are using the old asset pipeline which is not based off of sprockets and is probably pretty buggy.

## Support

Please file an issue if you see a problem. And enjoy! 
Also, I do accept pull-requests for bug fixes (and probably feature requests if it's reasonable...). Again, enjoy!
