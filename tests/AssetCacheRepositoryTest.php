<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\AssetCacheRepository;
use Codesleeve\AssetPipeline\SprocketsRepository;

class AssetCacheRepositoryTest extends PHPUnit_Framework_TestCase
{ 
    public function object()
    {
        return new AssetCacheRepository($this->app);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
        $this->app['env'] = 'production';
        $this->app['config']->data['cache'] = true;
        $this->app['asset'] = new SprocketsRepository($this->app);
    }

    public function testJavascripts()
    {
        $outcome = $this->object()->javascripts('application');

        $cache = $this->app['cache'];

    	$this->assertEquals('jquery.js;', $outcome);

        $this->assertEquals($cache->data['asset_pipeline_manager'], array('application' => $outcome));
    }

    public function testStylesheets()
    {
        $outcome = $this->object()->stylesheets('application');

        $cache = $this->app['cache'];

        $this->assertEquals('.foobar2{color:black}', $outcome);

        $this->assertEquals($cache->data['asset_pipeline_manager'], array('application' => $outcome));
    }

}