<?php

namespace spec\Codesleeve\AssetPipeline;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Codesleeve\Sprockets\SprocketsParser;
use Codesleeve\Sprockets\SprocketsGenerator;

class AssetPipelineSpec extends ObjectBehavior
{
    function let($parser, $generator, $jsComposer, $cssComposer)
    {
        $jsComposer->beADoubleOf('Codesleeve\AssetPipeline\Composers\JavascriptComposer');
        $cssComposer->beADoubleOf('Codesleeve\AssetPipeline\Composers\StylesheetComposer');

        $parser->beADoubleOf('Codesleeve\Sprockets\SprocketsParser');
        $generator->beADoubleOf('Codesleeve\Sprockets\SprocketsGenerator');

        $this->beConstructedWith($parser, $generator);
        $this->shouldHaveType('Codesleeve\AssetPipeline\AssetPipeline');
    }
}
/*

    function it_can_run_javascript_include_tag($parser, $jsComposer)
    {
        $absolutePaths = array('/absolute/file1');
        $webPaths = array('/assets/file1');
        $attributes = array('attribute');

        $parser->javascriptFiles('application')->willReturn($absolutePaths);
        $parser->absolutePathToWebPath($absolutePaths[0])->willReturn($webPaths[0]);

        $parser->get('config')->willReturn(array('javascript_include_tag' => $jsComposer));

        $jsComposer->process($webPaths, $absolutePaths, $attributes)->willReturn('good');

        $this->javascriptIncludeTag('application', $attributes)->shouldBeEqualTo('good');
    }

    function it_can_run_stylesheet_include_tag($parser, $cssComposer)
    {
        $absolutePaths = array('/absolute/file1');
        $webPaths = array('/assets/file1');
        $attributes = array('attribute');

        $parser->stylesheetFiles('application')->willReturn($absolutePaths);
        $parser->absolutePathToWebPath($absolutePaths[0])->willReturn($webPaths[0]);

        $parser->get('config')->willReturn(array('stylesheet_link_tag' => $cssComposer));

        $cssComposer->process($webPaths, $absolutePaths, $attributes)->willReturn('good');

        $this->stylesheetLinkTag('application', $attributes)->shouldBeEqualTo('good');
    }

    function it_can_tell_me_if_a_file_is_javascript_mime_type($parser)
    {
        $parser->stylesheetFiles('application')->willReturn($absolutePaths);

        $this->isJavascript('application.js')->shouldBeTrue();
    }
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


    /**
     * I could just mock Parser and Generator, but I am doing acceptance
     * testing here, and I want to also check our *REAL* config.php file
     * for errors.
    function it_can_use_the_real_world_config()
    {
        $jsComposer->beADoubleOf('Codesleeve\AssetPipeline\Composers\JavascriptComposer');
        $cssComposer->beADoubleOf('Codesleeve\AssetPipeline\Composers\StylesheetComposer');

        $base = __DIR__ . '/../../..';
        require $base . '/spec/fixtures/App.php';

        $config = include $base . '/src/config/config.php';
        $config['base_path'] = $base;
        $config['environment'] = "local";
        $config['javascript_include_tag'] = $jsComposer;
        $config['stylesheet_link_tag'] = $cssComposer;

        $parser = new SprocketsParser($config);
        $generator = new SprocketsGenerator($config);

        $this->beConstructedWith($parser, $generator);
        $this->shouldHaveType('Codesleeve\AssetPipeline\AssetPipeline');
    }



 */