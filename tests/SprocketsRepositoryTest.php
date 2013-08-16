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
    	$outcome = $this->object()->javascripts('application.js');
    	$this->assertContains('//= require jquery', $outcome);
    }

    public function testStylesheets()
    {
    	$outcome = $this->object()->stylesheets('application.css');
		$this->assertContains('*= require foobar', $outcome);
    }

    public function testTemplates()
    {
    	$outcome = $this->object()->javascripts('_jst_.js');
    	print $outcome;
    }

}    