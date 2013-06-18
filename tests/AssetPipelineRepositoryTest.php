<?php

use Mockery as m;
use Codesleeve\AssetPipeline\AssetPipelineRepository;

/**
 * @author  <kelt@dockins.org>
 * 
 * I was unable to use vfsStream for mocking the filesystem because
 * apparently Assetic isn't able to get the files from the 
 * vfs:// stream so I ended up making a fake real filesystem
 * under tests/root so I can test. Maybe it's using realpath() or
 * something? Oh well.. at least the code is tested somewhat right?
 */
class AssetPipelineRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Setup a project path, config and pipeline to use
     */
    public function setUp()
    {
        $this->projectPath = __DIR__ . '/root/project';
        $config_data = array(
            'asset-pipeline::path' => 'app/assets',
            'asset-pipeline::minify' => true,
            'asset-pipeline::compressed' => ['.min.', '-min.'],
            'asset-pipeline::ignores' => ['/test/', '/tests/'],
        );

        $this->config = $this->getMock('Config', array('get'));       
        $this->config->expects($this->any())
               ->method('get')
               ->with($this->anything())
               ->will($this->returnCallback(function($path) use ($config_data) {
                    if (array_key_exists($path, $config_data)) {
                        return $config_data[$path];
                    }

                    return $path;
               }));

        $this->pipeline = $this->new_pipeline();
    }

    /**
     * [tearDown description]
     * @return [type] [description]
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * [new_pipeline description]
     * @return [type] [description]
     */
    public function new_pipeline()
    {
        return new AssetPipelineRepository($this->projectPath, $this->config);
    }

    /**
     * [testCanCreateJavascript description]
     * @return [type] [description]
     */
    public function testCanCreateJavascript()
    {        
        $outcome = $this->pipeline->javascripts();

        $this->assertContains("alert('underscore.js')", $outcome);
        $this->assertContains("alert('backbone.js')", $outcome);
        $this->assertContains("alert('jquery.js')", $outcome);
        $this->assertContains("alert('file1.js')", $outcome);
        $this->assertContains("alert('file2.min.js');", $outcome);
        $this->assertContains("alert('file3-min.js');", $outcome);
        $this->assertContains("alert('app1.js')", $outcome);
    }

    /**
     * The coffeescript in our /coffeescripts/awesome.coffee should
     * get parsed into some javascript like what is below
     * 
     * @return [type] [description]
     */
    public function testCanCreateCoffeeScript()
    {
        $outcome = $this->pipeline->javascripts();
        $this->assertContains("function(x){return x*x}", $outcome);
    }

    /**
     * These test files shouldn't even be in the source code at all
     * so that allows us to keep our tests outside of the source but
     * still within the application assets folder
     * 
     * @return [type] [description]
     */
    public function testCanIgnorePatterns()
    {
        $outcome = $this->pipeline->javascripts();
        $this->assertNotContains("alert('test.js')", $outcome);
        $this->assertNotContains("alert('tests.js')", $outcome);
    }

    /**
     * We test for a semi-colon below because those would be removed
     * if we were running minification on those two files
     * 
     * @return [type] [description]
     */
    public function testCanIgnoreCompressedPatterns()
    {
        $outcome = $this->pipeline->javascripts();
        $this->assertContains("alert('file2.min.js');", $outcome);
        $this->assertContains("alert('file3-min.js');", $outcome);
    }

    /**
     * [testCanIncludeDifferentJavascriptDirectory description]
     * @return [type] [description]
     */
    public function testCanIncludeDifferentJavascriptDirectory()
    {
        $outcome = $this->pipeline->javascripts('application/scripts');
        $this->assertContains("alert('file1.js')", $outcome);
    }

    /**
     * [testCanCreateStylesheets description]
     * @return [type] [description]
     */
    public function testCanCreateStylesheets()
    {        
        $outcome = $this->pipeline->stylesheets();
        $this->assertContains('.styles1{color:red}', $outcome);
        $this->assertContains('.styles2{color:white}', $outcome);
        $this->assertContains('.styles3{color:blue}', $outcome);
    }

    /**
     * [testCanCreateStylesheetsWithLess description]
     * @return [type] [description]
     */
    public function testCanCreateStylesheetsWithLess()
    {
        $outcome = $this->pipeline->stylesheets();
        $this->assertContains('.box{color:#123456}', $outcome);
    }

    /**
     * [testCanHandleInvalidBaseDirectory description]
     * @expectedException InvalidArgumentException
     * @return [type] [description]
     */
    public function testCanHandleInvalidBaseDirectory()
    {
        $this->projectPath = __DIR__ . "/root/invalid_path";
        $pipeline = $this->new_pipeline();
    }

}
