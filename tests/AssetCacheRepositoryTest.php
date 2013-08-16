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
        $this->app['asset'] = new SprocketsRepository($this->app);
    }

    public function testJavascripts()
    {
        $outcome = $this->object()->javascripts('application');
        $cache = $this->app['cache'];

    	$this->assertEquals('jquery.js;', $outcome);

        $this->assertEquals($cache->data['asset_pipeline_recently_scanned_javascripts'], true);

        $this->assertTrue(array_key_exists('asset_pipeline_javascripts_last_updated_at', $cache->data));

        $this->assertEquals($cache->data['asset_pipeline_manager'], array('application' => 'jquery.js;'));

        $this->assertTrue(array_key_exists('asset_pipeline_recently_scanned_javascripts', $cache->timeout));
    }

    public function testStylesheets()
    {
        $outcome = $this->object()->stylesheets('application');
        $cache = $this->app['cache'];

        $this->assertEquals('.foobar2{color:black}', $outcome);

        $this->assertEquals($cache->data['asset_pipeline_recently_scanned_stylesheets'], true);

        $this->assertTrue(array_key_exists('asset_pipeline_stylesheets_last_updated_at', $cache->data));

        $this->assertEquals($cache->data['asset_pipeline_manager'], array('application' => $outcome));

        $this->assertTrue(array_key_exists('asset_pipeline_recently_scanned_stylesheets', $cache->timeout));
    }

}