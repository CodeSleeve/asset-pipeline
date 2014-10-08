<?php namespace Codesleeve\AssetPipeline;

use Codesleeve\Sprockets\SprocketsParser;
use Codesleeve\Sprockets\SprocketsGenerator;

require_once 'fixtures/App.php';

class AssetPipelineTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $base = __DIR__ . '/fixtures';

        $config = include __DIR__ . '/../src/config/config.php';
        $config['base_path'] = $base;
        $config['environment'] = "local";
        $config['javascript_include_tag'] = $this->getMock('Codesleeve\AssetPipeline\Composers\JavascriptComposer');
        $config['stylesheet_link_tag'] = $this->getMock('Codesleeve\AssetPipeline\Composers\StylesheetComposer');

        $parser = new SprocketsParser($config);
        $generator = new SprocketsGenerator($config);

        $this->base = $base;
        $this->config = $config;
        $this->pipeline = new AssetPipeline($parser, $generator);
    }

    public function testJavascriptIncludeTag()
    {
        $this->config['javascript_include_tag']->expects($this->once())->method('process');
        $this->pipeline->javascriptIncludeTag('application', array());
    }

    public function testStylesheetLinkTag()
    {
        $this->config['stylesheet_link_tag']->expects($this->once())->method('process');
        $this->pipeline->stylesheetLinkTag('application', array());
    }

    public function testIsJavascript()
    {
        $this->assertNotNull($this->pipeline->isJavascript('application.js'));
        $this->assertNull($this->pipeline->isJavascript('some.swf'));
        $this->assertNull($this->pipeline->isJavascript('application.css'));
    }

    public function testIsStylesheet()
    {
        $this->assertNull($this->pipeline->isStylesheet('application.js'));
        $this->assertNull($this->pipeline->isStylesheet('some.swf'));
        $this->assertNotNull($this->pipeline->isStylesheet('application.css'));
    }

    public function testIsFile()
    {
        $this->assertNotNull($this->pipeline->isFile('application.js'));
        $this->assertNotNull($this->pipeline->isFile('some.swf'));
        $this->assertNotNull($this->pipeline->isFile('application.css'));
    }

    public function testJavascript()
    {
        $output = $this->pipeline->javascript("{$this->base}/app/assets/javascripts/application.js");
        $this->assertNotEmpty($output);
    }

    public function testStylesheet()
    {
        $output = $this->pipeline->stylesheet("{$this->base}/app/assets/stylesheets/application.css");
        $this->assertNotEmpty($output);
    }

    public function testFile()
    {
        $output = $this->pipeline->file('some.swf');
        $this->assertEquals($output, "{$this->base}/app/assets/javascripts/some.swf");
    }

    public function testRegisterAssetPipelineFilters()
    {
        $output = $this->pipeline->registerAssetPipelineFilters();
        $this->assertEquals($output, $this->pipeline);
    }
}