<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\Directives\RequireFile;

class DirectivesRequireFileTest extends PHPUnit_Framework_TestCase
{ 
	public function object($manifestFile = 'application.js')
    {
		return new RequireFile($this->app, $manifestFile);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
    }

    public function testThing()
    {
    	$this->object();
    }
}