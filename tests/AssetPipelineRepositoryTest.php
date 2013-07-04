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
     * Allows us to test protected functions inside of the assetpipelinerepository
     * I don't really want to publicly expose these since they aren't really part
     * of the Asset facade but I do want to test them
     * 
     * @param  [type] $name [description]
     * @param  array  $args [description]
     * @return [type]       [description]
     */
    protected function callMethod($name, $args = array())
    {
        $class = new ReflectionClass($this->pipeline);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->pipeline, $args);
    }

    /**
     * Setup a project path, config and pipeline to use
     */
    public function setUp()
    {
        $this->projectPath = __DIR__ . '/root/project';
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
    public function new_pipeline($config_data_overrides = array())
    {
        $config_data = array_merge(array(
            'asset-pipeline::path' => 'app/assets',
            'asset-pipeline::minify' => true,
            'asset-pipeline::compressed' => array('.min.', '-min.'),
            'asset-pipeline::ignores' => array('/test/', '/tests/'),
            'asset-pipeline::manifest' => array(
                'javascripts' => array('vendors/*', '*'),
                'stylesheets' => array('*')
            )
        ), $config_data_overrides);

        $config = $this->getMock('Config', array('get'));       
        $config->expects($this->any())
             ->method('get')
             ->with($this->anything())
             ->will($this->returnCallback(function($path, $default = null) use ($config_data) {
                if (array_key_exists($path, $config_data)) {
                    return $config_data[$path];
                }
                return $default;
             }));

        return new AssetPipelineRepository($this->projectPath, $config);
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

    /**
     * [testCanHandleTemplates description]
     * @return [type] [description]
     */
    public function testCanHandleHtml()
    {
        $outcome = $this->pipeline->htmls();
        $this->assertContains('<div class="test">', $outcome);
        $this->assertContains('<script type="text/x-handlebars-template">', $outcome);
    }

    /**
     * [testCanHandleSpecificJsFile description]
     * @return [type] [description]
     */
    public function testCanHandleSpecificJsFile()
    {
        $outcome = $this->pipeline->javascripts('application/scripts/file1.js');
        $this->assertEquals("alert('file1.js');", $outcome);
    }

    /**
     * [testCanHandleSpecificCoffeeFile description]
     * @return [type] [description]
     */
    public function testCanHandleSpecificCoffeeFile()
    {
        $outcome = $this->pipeline->javascripts('javascripts/coffeescripts/awesome.coffee');
        $this->assertContains("function(x){return x*x}", $outcome);
    }

    /**
     * [testCanHandleSpecificCssFile description]
     * @return [type] [description]
     */
    public function testCanHandleSpecificCssFile()
    {
        $outcome = $this->pipeline->stylesheets('stylesheets/styles1.css');
        $this->assertContains('.styles1{color:red}', $outcome);
        $this->assertNotContains('.styles2{color:white}', $outcome);
    }

    /**
     * [testCanHandleSpecificLessFile description]
     * @return [type] [description]
     */
    public function testCanHandleSpecificLessFile()
    {
        $outcome = $this->pipeline->stylesheets('stylesheets/admin/testing.less');
        $this->assertContains('.box{color:#123456}', $outcome);
    }

    /**
     * [testCanHandleSpecificLessFile description]
     * @return [type] [description]
     */
    public function testCanHandleSpecificHtmlFile()
    {
        $outcome = $this->pipeline->htmls('templates/test.html');
        $this->assertContains('<div class="test">', $outcome);
        $this->assertNotContains('<script type="text/x-handlebars-template">', $outcome);
    }

    /**
     * [testPrecendenceTopDownForJs description]
     * @return [type] [description]
     */
    public function testPrecendenceForJs()
    {
        $outcome = $this->pipeline->javascripts();
        $this->assertContains("alert('backbone.js')", $outcome);
        $this->assertContains("alert('app1.js')", $outcome);

        $jquery = strpos($outcome, "alert('jquery.js')");
        $app1 = strpos($outcome, "alert('app1.js')");

        $this->assertLessThan($app1, $jquery);
    }

    /**
     * [testPrecendenceTopDownForJs description]
     * @return [type] [description]
     */
    public function testPrecendenceForCss()
    {
        $outcome = $this->pipeline->stylesheets();
        $this->assertContains(".box", $outcome);
        $this->assertContains(".styles1", $outcome);

        $box = strpos($outcome, ".box");
        $styles1 = strpos($outcome, ".styles1");

        $this->assertLessThan($styles1, $box);
    }

    /**
     * [testPrecendenceTopDownForJs description]
     * @return [type] [description]
     */
    public function testPrecendenceForHtml()
    {
        $outcome = $this->pipeline->htmls();

        $this->assertContains("atemplate", $outcome);
        $this->assertContains("{{something}}", $outcome);
        $this->assertContains("{{hmm}}", $outcome);

        $atemplate = strpos($outcome, "atemplate");
        $something = strpos($outcome, "{{something}}");
        $hmm = strpos($outcome, "{{hmm}}");

        $this->assertLessThan($atemplate, $something);
        $this->assertLessThan($hmm, $something);
    }

    /**
     * [testJavascriptLoadingOrder description]
     * @return [type] [description]
     */
    public function testJavascriptCustomLoadingOrder()
    {
        $pipeline = $this->pipeline;

        $manifest = array(
            'vendor/jquery/jquery.js',
            'file4.js',
            'vendor/*',
            '*'
        );

        $outcome = $pipeline->getFiles($pipeline->getPath('javascripts'), $manifest, array('js', 'coffee'));
        $expected = array(
            '/vendor/jquery/jquery.js',
            '/file4.js',
            '/vendor/backbone/underscore/underscore.js',
            '/vendor/backbone/backbone.js',
            '/vendor/file1.js',
            '/vendor/file2.min.js',
            '/vendor/file3-min.js',
            '/coffeescripts/awesome.coffee',
            '/test/test.js',
            '/tests/tests.js',
            '/app1.js'
        );

        array_walk($outcome, function(&$file) {
            $file = str_replace($this->pipeline->getPath('javascripts'), '', $file);
        });

        array_walk($expected, function(&$file) {
            $file = $this->normalizePath($file);
        });

        $item = 0;
        foreach($outcome as $result) {
            $this->assertEquals($result, $expected[$item++]);
        }
    }

    /**
     * [testJavascriptLoadingOrder description]
     * @return [type] [description]
     */
    public function testStylesheetLoadingOrder()
    {
        $pipeline = $this->pipeline;

        $manifest = array(
            'styles3.less',           
            '*'
        );

        $outcome = $pipeline->getFiles($pipeline->getPath('stylesheets'), $manifest, array('css', 'less'));

        $expected = array(
            '\styles3.less',
            '\admin\subdir\testing.less',
            '\admin\testing.less',
            '\styles1.css',
            '\styles2.css'
        );

        array_walk($outcome, function(&$file) {
            $file = str_replace($this->pipeline->getPath('stylesheets'), '', $file);
        });

        array_walk($expected, function(&$file) {
            $file = $this->normalizePath($file);
        });

        $item = 0;
        foreach($outcome as $result) {
            $this->assertEquals($result, $expected[$item++]);
        }
    }

    /**
     * [normalizePath description]
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    private function normalizePath($path) {
        return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
    }

}
