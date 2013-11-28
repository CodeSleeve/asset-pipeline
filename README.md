## What is Asset Pipeline?

For those of you familar with Rails asset pipeline and sprockets, you will hopefully feel right at home using this package.

For those of you unfamilar with Rails asset pipeline and sprockets, I suggest reading [introduction to directives](#intro_to_directives).

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

The next step is to open up your manifest file. Let's use the default at `app/assets/javascripts/application.js`.

```javascript

//
//= require jquery
//= require_tree .

```

This will bring in `/providers/assets/javascripts/jquery.min.js` and all files and folders within in `/app/assets/javascripts`

This is how you control your dependencies.

#### You can use the following directives.

  - **require** filename
 
    This brings in a specific asset file found within your `paths`.

  - **require_directory** some/directory
 
    This brings in assets only within some/directory (non-recurisve). You can also use '.' and '..' to resolve paths relative to the manifest file itself.

  - **require_tree** some/directory

    Just like require_directory except it recursively brings in all sub directories and files.

  - **require_self**

    This brings in the manifest file itself as an asset. This is already done on `require_tree .` if the manifest file is within that directory. Where you might want to use this is when you have a manifest file that does like `require_tree subdir/`

## License

The codesleeve asset pipeline is open-source software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Support

Before you do a pull request for a new feature please place in a proposal request. For bug fixes, please place in issues to track those, even if you fix the bug yourself and submit a pull request.

We use Travis CI for testing which you can see at: https://travis-ci.org/CodeSleeve/asset-pipeline

*Enjoy!* And have a nice day!
