<?php namespace Codesleeve\AssetPipeline;

use Illuminate\Support\ServiceProvider;
use Codesleeve\Sprockets\SprocketsParser;
use Codesleeve\Sprockets\SprocketsGenerator;

class AssetPipelineServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package('codesleeve/asset-pipeline');

		include __DIR__.'/../../routes.php';

		include_once __DIR__.'/AssetPipelineGlobalHelpers.php';

		$this->app['asset'] = $this->app->share(function($app)
		{
			$config = $app->config->get('asset-pipeline::config');
			$config['base_path'] = base_path();
			$config['environment'] = $app['env'];

			$parser = new SprocketsParser($config);
			$generator = new SprocketsGenerator($config);

			$pipeline = new AssetPipeline($parser, $generator);

			// let other packages hook into pipeline configuration
			$app['events']->fire('asset.pipeline.boot', $pipeline);

			return $pipeline->registerAssetPipelineFilters();
		});

		$this->app['assets.setup'] = $this->app->share(function($app)
        {
            return new Commands\AssetsSetupCommand;
        });

		$this->app['assets.clean'] = $this->app->share(function($app)
        {
            return new Commands\AssetsCleanCommand;
        });

		$this->app['assets.generate'] = $this->app->share(function($app)
        {
            return new Commands\AssetsGenerateCommand;
        });

		$this->commands('assets.setup');
		$this->commands('assets.clean');
		$this->commands('assets.generate');
	}

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerBladeExtensions();
	}

	/**
	 * Register custom blade extensions
	 *  - @stylesheets()
	 *  - @javascripts()
	 *
	 * @return void
	 */
	protected function registerBladeExtensions()
	{
		$blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

		$blade->extend(function($value, $compiler)
		{
			$matcher = $compiler->createMatcher('javascripts');

			return preg_replace($matcher, '$1<?php echo javascript_include_tag$2; ?>', $value);
		});

		$blade->extend(function($value, $compiler)
		{
			$matcher = $compiler->createMatcher('stylesheets');

			return preg_replace($matcher, '$1<?php echo stylesheet_link_tag$2; ?>', $value);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}