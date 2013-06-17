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

We need to make sure your environment is setup correctly because the Asset Pipeline caches assets differently on a production environment than the development environment. So it is important that you put your development machine in the `bootstrap/start.php` or your assets will be cached. If you're unsure what to put here for your machine name then do a `dd (gethostname());` to find out.

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

    http://<your laravel project>/assets/javascripts.js
    
Or for styles:

    http://<your laravel project>/assets/stylesheets.js


Modify your layout view (or any view) to have something similar to this in it

```php
  <script src="<?= asset('assets/javascripts.js') ?>"></script>
```

And a similar line of code for styles

```php
  <link href="<?= asset('assets/stylesheets.css') ?>" rel="stylesheet" type="text/css">
```

### Do I have to use javascripts.js though?

Basically any folder inside your assets directory can be routed too. (if you're curious about this then take a look at `vendor/codesleeve/asset-pipeline/src/routes.php`)

So say you don't want `/assets/javascripts.js` then you could create a directory `/assets/application` and place `file1.css` and `file1.js` inside that folder. 

Then point your browser to:

    http://<your laravel project>/assets/application.js

or

    http://<your laravel project>/assets/application.js


Nifty huh? This allows for you to control which assets are released if you choose to do so. But there's nothing wrong with
putting them all in `app/assets/javascripts/` and `app/assets/stylesheets` either (in fact, that is a good standard practice).

### Images? Fonts?

One thing you might be wondering is, how do I get my font-awesome fonts or my images? Assuming you do relative paths 
in your css (which any good designer should) then place your fonts and images inside of the laravel `public/` folder.

Font awesome just looks for '../font/<files>' so this works out nicely. **todo note we might need to use a uri re-write filter (i'll have to look into it)**


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


If you want to point your application somewhere besides `http://<laravel project dir>/assets/javascripts.js` you can change this

```php
	'routing' => [
		'prefix' => '/assets'
	],
```

This path is relative to your laravel root base directory `base_path()`

```php
  'path' => 'app/assets',
```

If you want to turn off minification (for debugging perhaps?) you can do that easily here.

```php
  'minify' => true,
```

We won't compress files that are already compressed.

You might ask why not? We are using a php-min compressor (no YUI jar files around these parts!). However, there isn't
an option (that I could find) to turn off obscurify.

So, for example, if you use handlebars.js then you will likely get an error when that file is compressed because 
(from what I can tell) when that handlebars.js is obscurified it just flat out breaks. So a work around for this is 
to go ahead and compress handlebars.js yourself using `http://refresh-sf.com/yui/` and make sure 
`Minify only, no symbol obfuscation.` is checked.

```php
'compressed' => [
		'.min.', 
		'-min.'
	],
```

Next up is the `ignores` config. This allows us to ignore certain files or directories if we choose. By default we ignore the most common name for test folders.

```php
	'ignores' => [
		'/test/',
		'/tests/'
	],
```

