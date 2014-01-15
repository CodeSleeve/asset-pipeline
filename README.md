## What is Asset Pipeline?

For those of you familar with Rails asset pipeline and sprockets, you will hopefully feel right at home using this package.

For those of you unfamilar with Rails asset pipeline and sprockets, I suggest reading [introduction to directives](#introduction-to-directives).

## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `codesleeve/asset-pipeline`.

It might look something like:

```php
  "require": {
    "laravel/framework": "4.1.*",
    "codesleeve/asset-pipeline": "dev-master"
  }
```

Next, update Composer from the Terminal:

```php
    composer update
```

Once this operation completes, add the service provider. Open `app/config/app.php`, add the following items to the providers array.

```php
    'Codesleeve\AssetPipeline\AssetPipelineServiceProvider',
```

Next optionally, ensure your environment is setup correctly because by default the asset pipeline will cache and and minify assets on a production environment.

Inside `bootstrap/start.php`

```php
  $env = $app->detectEnvironment(array(
    'local' => array('your-machine-name'),
  ));
```

Run the `artisan` command from the Terminal for the `assets:setup` command. This will create the default folder structure for you.

```php
    php artisan assets:setup
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

To create a custom package config for [configuration of the asset pipeline.](#configuration) run

```php
  php artisan config:publish codesleeve/asset-pipeline
```

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
    'app/assets/images',
    'lib/assets/javascripts',
    'lib/assets/stylesheets',
    'lib/assets/images',
    'provider/assets/javascripts',
    'provider/assets/stylesheets',
    'provider/assets/images'
  ),
```
  
These are the directories we search for files in. You can think of this like PATH environment variable on your OS. We search for files in the path order listed below.

### mimes

```php
  'mimes' => array(
      'javascripts' => array('.js', '.js.coffee', '.coffee', '.html', '.min.js'),
      'stylesheets' => array('.css', '.css.less', '.css.scss', '.less', '.scss', '.min.css'),
  ),
```

In order to know which mime type to send back to the server we need to know if it is a javascript or stylesheet type. If the extension is not found below then we just return a regular download. You should include all extensions in your `filters` here or you will likely experience unexpected behavior. This should allow developers to mix javascript and css files in the same directory.

### filters

```php
  'filters' => array(
    '.min.js' => array(

    ),
    '.min.css' => array(
      new Codesleeve\AssetPipeline\Filters\URLRewrite,
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
      new Codesleeve\AssetPipeline\Filters\URLRewrite,
      new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
    ),
    '.css.less' => array(
      new Assetic\Filter\LessphpFilter,
      new Codesleeve\AssetPipeline\Filters\URLRewrite,
      new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
    ),
    '.css.scss' => array(
      new Assetic\Filter\ScssphpFilter,
      new Codesleeve\AssetPipeline\Filters\URLRewrite,
      new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
    ),
    '.less' => array(
      new Assetic\Filter\LessphpFilter,
      new Codesleeve\AssetPipeline\Filters\URLRewrite,
      new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
    ),
    '.scss' => array(
      new Assetic\Filter\ScssphpFilter,
      new Codesleeve\AssetPipeline\Filters\URLRewrite,
      new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\CssMinFilter, App::environment()),
    ),
    '.html' => array(
      new Codesleeve\AssetPipeline\Filters\JST,
      new EnvironmentFilter(new Codesleeve\AssetPipeline\Filters\JSMinPlusFilter, App::environment()),
    )
  ),
```

In order for a file to be included with sprockets, the extension needs to be listed here. We can also preprocess those extension types with Assetic Filters.

### cache

```php
  'cache' => new Codesleeve\AssetPipeline\Filters\CacheEnvironmentFilter(new Assetic\Cache\FilesystemCache(App::make('path.storage') . '/cache/asset-pipeline'), App::environment()),
```

By default we cache on production environment only. This CacheEnvironmentFilter only runs when `App::environment()` is 'production'. If you want to specify other environments you can pass in an optional 3rd parameter to the constructor, i.e. `array('production', 'staging')`.

Also by default we use Assetic's FilesystemCache but you can create your own [CacheInterface](https://github.com/kriswallsmith/assetic/blob/master/src/Assetic/Cache) if you want to handle caching differently.

If you want to clear your cache you will need to run

```php
   php artisan assets:clean
```

This will clear the cached files `application.js` and `application.css`. If you have other files you want cleaned then you can pass them as parameters via `-f` or `--file`

```php
  php artisan assets:clean -f interior/application.js -f exterior/application.js -f interior/application.css -f exterior/application.css
```

### concat

```php
  'concat' => array('production', 'local')
```

This allows us to turn on the asset concatenation for the specific environments listed. For performance reasons, we recommend keeping this turned on except if you are trying to troubleshoot an javascript issue.


### directives

```php
  'directives' => array(
    'require ' => new Codesleeve\Sprockets\Directives\RequireFile,
    'require_directory' => new Codesleeve\Sprockets\Directives\RequireDirectory,
    'require_tree' => new Codesleeve\Sprockets\Directives\RequireTree,
    'require_self' => new Codesleeve\Sprockets\Directives\RequireSelf,
  ),
```

These are the directives we try to process inside of manifest files. This allows you to swap out, add new, modify existing directives for your pipeline setup.


### javascript_include_tag

```php
  'javascript_include_tag' => new Codesleeve\AssetPipeline\Composers\JavascriptComposer,
```

When you do `<?= javascript_include_tag() ?>` this composer class will be invoked. This allows you to compose your own javascript tags if you want to modify how javascript tags are printed.


### stylesheet_link_tag

```php
  'stylesheet_link_tag' => new Codesleeve\AssetPipeline\Composers\StylesheetComposer,
```

When you do `<?= stylesheet_link_tag() ?>` this composer class will be invoked. This allows you to compose your own stylesheet tags if you want to modify how stylesheet tags are printed.


### controller_action

```php
  'controller_action' => '\Codesleeve\AssetPipeline\AssetPipelineController@file',
```

This is the controller action the pipeline routes all incoming requests to. If you ever want to swap this out for your own implementation you can edit this. This allows you to completely control how assets are being served to the browser.


### sprockets_filter

```php
  'sprockets_filter' => '\Codesleeve\Sprockets\SprocketsFilter',
```

When concatenation is turned on, all assets fetched from the sprockets generator are filtered through this filter class. This allows us to modify the sprockets filter if we need to behave differently.


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

### Can I hook in my own packages for asset pipeline?

Yes. By using the event listener `asset.pipeline.boot` you can intercept the pipeline object and modify the configuration array to your own will. But remember with great power comes great responsibility. Here is an example,

```php

Event::listen('asset.pipeline.boot', function($pipeline)
{
    $config = $pipeline->getConfig();
    $config['directives']['awesome_directive'] = new MyAwesomeDirective;
    $pipeline->setConfig($config);
});

```
So what does MyAwesomeDirective look like? That is entirely up to you.

```php
class MyAwesomeDirective extends Codesleeve\Sprockets\Directives\RequireFile
{
  public function process($param)
  {
      $files = array();

      if (App::environment() === 'local' && $param == 'foobar')
      {
        // do chicken dance and add some files to array
        // alos, this needs to be an absolute path to file
        $files[] = __DIR__ . '/chicken/dance.js';
      }

      return $files;
  }
}
```

### Can I use nginx

You may have to configure nginx. The files are not in `/assets/` so you will likely get a 404. Thus you need to tell nginx to route the request through `index.php` if the file is not found. This can be accomplished with something like this:

```js
  location ~ ^/(assets)/{
    try_files $uri $uri/ /index.php?$args;
    expires max;
    add_header Cache-Control public;
  }
```

### Can I use an older version of asset pipeline

The asset pipeline has been refactored to be smarter, cleaner, better. However, with that brought along breaking changes because things work differently. So if you have older existing projects that were pointing to `dev-master`, you should probably find a tag version that works for you. If it just recently broke, try the latest tag minus 1. Also, I typically push out my changes to `dev-testing`.

## License

The codesleeve asset pipeline is open-source software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Support

Before you do a pull request for a new feature please place in a proposal request. For bug fixes, please place in issues to track those, even if you fix the bug yourself and submit a pull request. All pull requests go to `dev-testing` before `dev-master`.

We use Travis CI for testing which you can see at: https://travis-ci.org/CodeSleeve/asset-pipeline

*Enjoy!* And have a nice day!
