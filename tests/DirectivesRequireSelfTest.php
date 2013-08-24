<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\Directives\RequireSelf;

class DirectivesRequireSelfTest extends PHPUnit_Framework_TestCase
{ 
	public function object($manifestFile = 'application.js')
    {
		return new RequireSelf($this->app, $manifestFile);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
    }

    public function testThing()
    {
    	$this->object();
    }

    public function testProcess()
    {
        $outcome = $this->object()->process();
        $this->assertEquals($outcome, array('app/assets/javascripts/application.js'));
    }
}