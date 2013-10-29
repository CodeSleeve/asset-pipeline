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
    
    public function testExtensions()
    {
        $expected = array_keys($this->app['config']->get('filters'));
        $outcome = $this->object()->extensions();
        $this->assertEquals($expected, $outcome);
    }
    
    public function testExtensionsOnlyJavascripts()
    {
        $expected = array('.min.js', '.js', '.js.coffee');
        $outcome = $this->object()->extensions(AssetFilters::JAVASCRIPTS);
        $this->assertEquals($expected, $outcome);
    }
    
    public function testExtensionsOnlyStylesheets()
    {
        $expected = array('.min.css', '.css', '.css.less', '.css.scss');
        $outcome = $this->object()->extensions(AssetFilters::STYLESHEETS);
        $this->assertEquals($expected, $outcome);
    }
    
    public function testExtensionsOnlyOthers()
    {
        $expected = array('.html');
        $outcome = $this->object()->extensions(AssetFilters::OTHERS);
        $this->assertEquals($expected, $outcome);
    }
}