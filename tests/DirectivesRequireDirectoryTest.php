<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\Directives\RequireDirectory;

class DirectivesRequireDirectoryTest extends PHPUnit_Framework_TestCase
{ 
	public function object($manifestFile = 'application.js')
    {
		return new RequireDirectory($this->app, $manifestFile);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
    }

    public function testItWorks()
    {
    	$outcome = $this->object()->process('./apps');
        $this->assertEquals($outcome, array('app/assets/javascripts/apps/cool.js'));
    }
}