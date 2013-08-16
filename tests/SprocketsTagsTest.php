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

    public function testjavascriptTag()
    {
    	$outcome = $this->object()->javascriptIncludeTag();

    	$this->assertEquals('<script src="/assets/jquery.js"></script>' . PHP_EOL, $outcome);
    }

    public function testStylesheetTag()
    {
        $outcome = $this->object()->stylesheetLinkTag();
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="/assets/foobar2.css">' . PHP_EOL, $outcome);
    }

    public function testImageTag()
    {
        $outcome = $this->object()->imageTag('awesome/durka.png');
        $this->assertEquals('<img src="/assets/awesome/durka.png">', $outcome);
    }

}    