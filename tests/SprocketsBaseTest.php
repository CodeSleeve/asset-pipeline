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

    public function testFullPath()
    {
    // 	$outcome = $this->object()->getFullPath('application.js');
    //     $outcome = str_replace($this->app['path.base'] . '/', '', $outcome);
    //     $this->assertEquals($outcome, 'app/assets/javascripts/application.js');
    }

    // public function testFullPathWithInvalidPathInIt()
    // {
    //     $this->setExpectedException('Codesleeve\AssetPipeline\Exceptions\InvalidPath');
    //     $outcome = $this->object()->getFullPath('foobar/doesnt/exist/application.js');
    // }

    // public function testFullPathWithRelativePathInIt()
    // {
    //     $this->setExpectedException('Codesleeve\AssetPipeline\Exceptions\InvalidPath');
    //     $outcome = $this->object()->getFullPath('../application.js');
    // }

    // public function tesUrlPath()
    // {
    //     $outcome = $this->object()->getUrlPath('application.js');
    // }
}