## What is Asset Pipeline?

For those of you familar with Rails asset pipeline and sprockets, you will hopefully feel right at home using this package.

For those of you unfamilar with Rails asset pipeline and sprockets, I suggest reading [introduction to directives](#introduction-to-directives).

## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `codesleeve/asset-pipeline`.

It might look something like:

```php
  "require": {
    "laravel/framework": "4.0.*",
    "codesleeve/asset-pipeline": "dev-refactor"
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

Optionally, ensure your environment is setup correctly because by default the asset pipeline will cache and and minify assets on a production environment.

Inside `bootstrap/start.php`

```php
  $env = $app->detectEnvironment(array(
    'local' => array('your-machine-name'),
  ));
```

Run the `artisan` command from the Terminal for the `assets:generate` command. This will create the default folder structure for you.

```php
    php artisan assets:generate
```

It is recommended to create a custom package config for [configuration of the asset pipeline.](#configuration)

```php
  php artisan config:publish codesleeve/asset-pipeline
```

## Usage

Place these lines into your Laravel view/layout

```php
    <?= stylesheet_link_tag() ?>
    <?= javascript_include_tag() ?>
```

This will generate a listing of script and link tags for all the dependencies listed in `app/assets/application.js` and `app/assets/application.css`. 

#### Parameters

If you'd like to control which manifest file you'd like to use and even put attributes on the tags. This follows the same pattern rails uses, so for example, if we have this:

```php
    <?= javascript_include_tag('interior/application', ['data-foo' => 'bar']) ?>
```

and assuming `concat => array('production')` and we are on a production environment then this generates

```php
    <script src="assets/interior/application.js" data-foo="bar"></script>
```    

## Introduction to Directives

Let's open up the default javascript manifest file `app/assets/javascripts/application.js`. You should see something like

```javascript

//
//= require jquery
//= require_tree .

```

This will bring in the file  `/providers/assets/javascripts/jquery.min.js` and also all files and sub directories within in `/app/assets/javascripts` folder.

This is how you control your dependencies. Simple right?

#### Here is a list of directives you can use

  - **require** filename
 
    This brings in a specific asset file found within your `paths`.

  - **require_directory** some/directory
 
    This brings in assets only within some/directory (non-recurisve). You can also use '.' and '..' to resolve paths relative to the manifest file itself.

  - **require_tree** some/directory

    Just like require_directory except it recursively brings in all sub directories and files.

  - **require_self**

    This brings in the manifest file itself as an asset. This is already done on `require_tree .` if the manifest file is within that directory. Where you might want to use this is when you have a manifest file that does like `require_tree subdir/`

## Configuration

### routing array

```php
  'routing' => array(
    'prefix' => '/assets'
  ),
```

Sprockets parser also uses this to help generate the correct web path for our assets. It is also used by the asset pipeline for routing.


### paths

```php
  'paths' => array(
    'app/assets/javascripts',
    'app/assets/stylesheets',
    'lib/assets/javascripts',
    'lib/assets/stylesheets',
    'provider/assets/javascripts',
    'provider/assets/stylesheets'
  ),
```
  
These are the directories we search for files in. You can think of this like PATH environment variable on your OS. We search for files in the path order listed below.

### filters

```php
  'filters' => array(
    '.min.js' => array(

    ),
    '.js' => array(
      new Codesleeve\AssetPipeline\Filters\MinifyJS(App::environment())
    ),
    '.min.css' => array(

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
```

In order for a file to be included with sprockets, the extension needs to be listed here. We can also preprocess those extension types with Assetic Filters.

### mimes

```php
  'mimes' => array(
      'javascripts' => array('.js', '.js.coffee', '.min.js', '.html'),
      'stylesheets' => array('.css', '.css.less', '.css.scss', '.min.css'),
  ),
```

In order to know which mime type to send back to the server we need to know if it is a javascript or stylesheet type. If the extension is not found below then we just return a regular download. You should include all extensions in your `filters` here or you will likely experience unexpected behavior. This should allow developers to mix javascript and css files in the same directory.

### cache

```php
  'cache' => new Assetic\Cache\FilesystemCache(storage_path() . '/cache/asset-pipeline'),
```

By default we cache all assets. This will greatly increase performance. However, it is up to the developer to determine how the pipeline should tell Assetic to cache assets. 

You can create your own [CacheInterface](https://github.com/kriswallsmith/assetic/blob/master/src/Assetic/Cache) if you want to handle caching differently. If you want to turn off caching completely you can use a CacheInterface that comes already bundled with asset pipeline `Codesleeve\AssetPipeline\Filters\FilesNotCached`

### concat

```php
  'concat' => array('production', 'local')
```

This allows us to turn on the asset concatenation for the specific environments listed. I recommend keeping this turned on except if you are trying to troubleshoot an javascript issue.

## FAQ

### Can I modify the asset pipeline config at runtime?

You can listen to `asset.pipeline.boot` event and this will pass the pipeline object to you for any changes you might want to make.

```php
  Event::listen('asset.pipeline.boot', function($pipeline) {
    $config = $pipeline->getConfig();

    $config['paths'][] = 'some/special/javascripts';
    $config['paths'][] = 'some/special/stylesheets';

    $config['mimes']['javascripts'][] = '.foo.bar';

    $config['filters']['.foo.bar'] = array(
      new My\Special\Filter
    );

    $pipeline->setConfig($config);
  });
```

This code registers two new paths and creates a new extension called .foo.bar that is filtered with `My\Special\Filter`. Using the event listener allows us to extend the functionality of the asset pipeline in separate packages.

### Can I do Javascript Templates (JST)

Yes. Out of the box you can use .html files somewhere within your `app/assets/javascripts` folder and you will be given a JST array on your front end javascript that contains the html page. If you want a different extension (i.e. jst.hbs) you will need to bring that in.

### Can I do images, fonts, and other files?

Files that are not in the `mime` and `filters` array of our configuration will be treated as regular files. You can still access them via web urls, but they will trigger a `Response::download` instead of being served as javascript or stylesheet files.

### Can I do conditional includes?

There is no built-in mechanism to conditionally include assets via the asset pipeline. One technique  I use is to namespace my html page in my layout view. I create a View::share that always contains the current route for me.

```html
  <html class="<?= $currentRoute ?>" lang="en">
```

This allows me to prefix my css with the route. So if I only wanted a blue background on the home page I could do something like this.

```css
  html.home.index body {
    background-color: blue;
  }
```

If you are trying to conditionally include javascript on a page, I recommend the use of bindings. Create specific scripts that will only be run when certain data attributes or class names are found.

```js
  $('[data-foo]').each(function()
  {
    console.log('only run when we find data-foo="" attribute');
  });
```

And so if we have an element like this it will run

```html
  <a data-foo="bar" href="#">Hey there</a>
```

If you find yourself having issues with conditionally including assets your best bet may be to break apart your manifest files into sections that make sense for your application. For example, if your application is silo'ed into admin section and user section then it probably makes sense to have a separate manifest file for each section.

## License

The codesleeve asset pipeline is open-source software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Support

Before you do a pull request for a new feature please place in a proposal request. For bug fixes, please place in issues to track those, even if you fix the bug yourself and submit a pull request.

We use Travis CI for testing which you can see at: https://travis-ci.org/CodeSleeve/asset-pipeline

*Enjoy!* And have a nice day!
