<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\SprocketsRepository;

class SprocketsRepositoryTest extends PHPUnit_Framework_TestCase
{ 
    public function object()
    {
        return new SprocketsRepository($this->app);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
    }

    public function testJavascripts()
    {
    	$outcome = $this->object()->javascripts('application');
    	$this->assertContains('//= require jquery', $outcome);
    }

    public function testJavascriptsProd()
    {
        $this->app['env'] = 'production';
        $outcome = $this->object()->javascripts('application');
        $this->assertEquals('jquery.js;', $outcome);
    }

    public function testStylesheets()
    {
    	//$outcome = $this->object()->stylesheets('application');
		//$this->assertContains('*= require foobar2', $outcome);
    }

    public function testStylesheetsProd()
    {
    	$this->app['env'] = 'production';
    	$outcome = $this->object()->stylesheets('application');
        print $outcome;
    }
}    