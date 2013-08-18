<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\AssetFilters;

class AssetFiltersTest extends PHPUnit_Framework_TestCase
{ 
    public function object()
    {
        return new AssetFilters($this->app);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
    }

    public function testHasValidExtensions()
    {
    	$outcome = $this->object()->hasValidExtension('.js');
        $this->assertEquals('.js', $outcome);
    }

    public function testMatching()
    {
        $outcome = $this->object()->matching('cool.css.less');
        $this->assertGreaterThan(0, count($outcome));
    }

    public function testCanAddFilters()
    {
        $object = $this->object();
        $object->filters['.jst.hbs'] = 'awesome';
        $outcome = $object->matching('cool.jst.hbs');
        $this->assertEquals($outcome, 'awesome');

    }
}