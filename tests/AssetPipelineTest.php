<?php namespace Codesleeve\AssetPipeline;

use Codesleeve\Sprockets\SprocketsParser;
use Codesleeve\Sprockets\SprocketsGenerator;

class AssetPipelineTest extends TestCase
{ 
    public function setUp()
    {
        //require_once('App.php');

        $config = include __DIR__ . "/../src/config/config.php";       
        $config['base_path'] = __DIR__ . '/fixtures';
        $config['environment'] = "local";

        $parser = new SprocketsParser($config);
        $generator = new SprocketsGenerator($config);

        $this->pipeline = new AssetPipeline($parser, $generator);
    }

    public function testJavascriptIncludeTag()
    {
        //$this->pipeline->javascriptIncludeTag('application', array());
    }

    public function testStylesheetLinkTag()
    {

    }

    public function testIsJavascript()
    {

    }

    public function testIsStylesheet()
    {

    }

    public function testIsFile()
    {

    }

    public function testJavascript()
    {

    }

    public function testStylesheet()
    {

    }

    public function testFile()
    {

    }
}