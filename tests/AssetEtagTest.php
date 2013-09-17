<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\AssetEtag;
use Codesleeve\AssetPipeline\SprocketsRepository;

class AssetEtagTest extends PHPUnit_Framework_TestCase
{ 
    public function object()
    {
        return new AssetEtag($this->app);
    }

    public function setUp()
    {
        $this->app = App::make(__DIR__);
        $this->app['env'] = 'production';
        $this->app['config']->data['cache'] = true;
        $this->app['asset'] = new SprocketsRepository($this->app);
    }

    public function testCreation()
    {
        $this->object();
    }
}