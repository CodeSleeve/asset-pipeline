<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\AssetPaths;

class AssetPathsTest extends PHPUnit_Framework_TestCase
{ 
    public function object()
    {
        return new AssetPaths($this->app);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
    }

    public function testGetJavascriptPaths()
    {
    	$outcome = $this->object()->get('javascripts');
        $this->assertEquals($outcome, array(
            'app/assets/javascripts',
            'lib/assets/javascripts',
            'vendor/assets/javascripts'));
    }

    public function testGetStylesheetPaths()
    {
        $outcome = $this->object()->get('stylesheets');
        $this->assertEquals($outcome, array(
            'app/assets/stylesheets',
            'lib/assets/stylesheets',
            'vendor/assets/stylesheets'));
    }

    public function testGetAllPaths()
    {
        $object = $this->object();
        $outcome = $object->get('all');
        $this->assertEquals($outcome, array_keys($object->paths));
    }

    public function testGetBogusIncludePath()
    {
        $outcome = $this->object()->get('bogus');
        $this->assertEquals($outcome, array());
    }

    public function testPathThatIsBothStylesheetAndJavascript()
    {
        $object = $this->object();
        $object->add('another/path', 'javascripts,stylesheets');

        $outcome = $object->get('javascripts');

        $this->assertEquals($outcome, array(
            'another/path',
            'app/assets/javascripts',
            'lib/assets/javascripts',
            'vendor/assets/javascripts'
        ));

        $outcome = $object->get('stylesheets');
        $this->assertEquals($outcome, array(
            'another/path',
            'app/assets/stylesheets',
            'lib/assets/stylesheets',
            'vendor/assets/stylesheets'
        ));
    }

}