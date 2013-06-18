<?php namespace Codesleeve\AssetPipeline;

use Illuminate\Support\ServiceProvider;

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
		
		$this->app['asset-pipeline'] = $this->app->share(function($app) {
            return new AssetPipelineRepository(base_path(), $app['config']);
        });

		$this->app['generate.assets'] = $this->app->share(function($app)
		{
			return new Commands\GenerateAssetsCommand;
		});

		$this->commands('generate.assets');
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