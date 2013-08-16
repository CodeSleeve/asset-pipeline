<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\Directives\IncludeJST;

class DirectivesIncludeJSTTest extends PHPUnit_Framework_TestCase
{ 
	public function object($manifestFile = 'application.js')
    {
		return new IncludeJST($this->app, $manifestFile);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
    }

    public function testItWorks()
    {
        $outcome = $this->object()->process();
        $this->assertEquals($outcome, array('_jst_.js'));
    } 
}