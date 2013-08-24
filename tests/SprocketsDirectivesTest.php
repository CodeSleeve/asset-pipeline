<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\SprocketsDirectives;

class SprocketsDirectivesTest extends PHPUnit_Framework_TestCase
{
    public function object()
    {
		return new SprocketsDirectives($this->app);
    }

    public function setUp()
    {
        $this->app = App::make(__DIR__);
    }

    public function testNoManifest()
    {
		$outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest01.js');
		$this->assertEquals($outcome, array());
    }

    public function testWithRequireFile()
    {
		$outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest02.js');
		$this->assertEquals(array(
            'vendor/assets/javascripts/jquery.js'
        ), $outcome);
    }

    public function testWithMultiplesOfRequireFile()
    {
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest03.js');
        $this->assertEquals($outcome, array(
            'vendor/assets/javascripts/jquery.js'
        ), $outcome);
    }

    public function testWithMultiplesOfDifferntRequireFile()
    {
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest04.js');
        $this->assertEquals($outcome, array(
            'vendor/assets/javascripts/jquery.js',
            'vendor/assets/javascripts/foobar/foobar.js'
        ), $outcome);
    }

    public function testWithDocBlockComments()
    {
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest05.js');
        $this->assertEquals(array(
            'vendor/assets/javascripts/jquery.js'
        ), $outcome);
    }

    public function testWhenRequireFileDoesnotExist()
    {
        $this->setExpectedException('Codesleeve\AssetPipeline\Exceptions\InvalidPath');
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest06.js');
    }

    public function testWhenRequireFileHasSpacesInIt()
    {
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest07.js');
        $this->assertEquals(array(
            'vendor/assets/javascripts/folder with spaces/file1.js'
        ), $outcome);
    }

    public function testWhenRequireFileWorksWithOtherPaths()
    {
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest08.js');
        $this->assertEquals(array(
            'vendor/assets/javascripts/jquery.js',
            'lib/assets/javascripts/cool.js',
            'app/assets/javascripts/apps/cool.js'
        ), $outcome);
    }

    public function testWhenDuplicateRequireFileExistsWhichOneGetsLoaded()
    {
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest09.js');
        $this->assertEquals(array(
            'app/assets/javascripts/test.js',
        ), $outcome);
    }

    public function testUnknownDirectiveGiven()
    {
        $this->setExpectedException('Codesleeve\AssetPipeline\Exceptions\UnknownSprocketsDirective');
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require/manifest10.js');
    }

    public function testRequireDirectoryWithLeadingSlash()
    {
        $this->setExpectedException('InvalidArgumentException');
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require_directory/manifest01.js');
    }

    public function testRequireDirectoryRelativeDir()
    {
    	$this->app["path.base"] = __DIR__ . '/fixtures/require_directory';
    	$this->app['config']->set('paths', array('manifest02' => 'javascripts'));

        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require_directory/manifest02/manifest02.js');
        $this->assertEquals(array(
            'manifest02/and even.css.less',
            'manifest02/awesome.js.coffee',
            'manifest02/manifest02.js',
            'manifest02/more awesome.css'
        ), $outcome);
    }

    public function testRequireDirectoryWithDotDot()
    {
        $this->setExpectedException('InvalidArgumentException');
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require_directory/manifest03.js');
    }

    public function testRequireTreeIncludesRecursively()
    {
        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require_tree/manifest01.js');
        $this->assertEquals(array(
            'app/assets/javascripts/apps/test/whoa.html',
            'app/assets/javascripts/apps/cool.js',
            'app/assets/javascripts/application.js',
            'app/assets/javascripts/application1.js',
            'app/assets/javascripts/application2.js',
            'app/assets/javascripts/test.js'
        ), $outcome);
    }

    public function testRequireSelf()
    {
        $this->app["path.base"] = __DIR__ . '/fixtures/require_self';
        $this->app['config']->set('paths', array('manifest01' => 'javascripts'));

        $outcome = $this->object()->getFilesFrom(__DIR__ . '/fixtures/require_self/manifest01/manifest01.js');
        $this->assertEquals(array('manifest01/manifest01.js'), $outcome);
    }
}    