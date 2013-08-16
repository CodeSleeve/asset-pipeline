<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\SprocketsTags;

class SprocketsTagsTest extends PHPUnit_Framework_TestCase
{ 
    public function object()
    {
        return new SprocketsTags($this->app);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
    }

    public function testInitialize()
    {
    	$this->assertInstanceOf('Codesleeve\\AssetPipeline\\SprocketsTags', $this->object());
    }

    public function testjavascriptIncludeTag()
    {
    	$outcome = $this->object()->javascriptIncludeTag();

    	$this->assertEquals('<script src="/jquery.js"></script>' . PHP_EOL, $outcome);
    }

}    