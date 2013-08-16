<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\SprocketsBase;

class SprocketsBaseTest extends PHPUnit_Framework_TestCase
{
    public function object()
    {
		return new SprocketsBase($this->app);
    }

    public function setUp()
    {
        $this->app = App::make(__DIR__);
    }

    public function testUrlPath()
    {
        $outcome = $this->object()->getUrlPath('application.css');
        $this->assertEquals('/assets/application.css', $outcome);
    }

    public function testFullPath()
    {
        $outcome = $this->object()->getFullPath('application');
        $this->assertContains('/root/sprockets/app/assets/javascripts/application.js', $outcome);
    }

    public function testFullPathWithIncludes()
    {
        $outcome = $this->object()->getFullPath('application', 'stylesheets');
        $this->assertContains('/root/sprockets/app/assets/stylesheets/application.css', $outcome);
    }

    public function testRelativePath()
    {
        $outcome = $this->object()->getRelativePath('application');
        $this->assertEquals('app/assets/javascripts/application.js', $outcome);
    }

    public function testRelativePathWithIncludes()
    {
        $outcome = $this->object()->getRelativePath('application', 'stylesheets');
        $this->assertEquals('app/assets/stylesheets/application.css', $outcome);
    }

}