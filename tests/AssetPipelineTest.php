<?php namespace Codesleeve\AssetPipeline;

use PHPUnit_Framework_TestCase;

class AssetPipelineTest extends PHPUnit_Framework_TestCase
{ 
    public function setUp()
    {
        $generator = null; // mock sprockets generator
        $parser = null; // mock sprockets parser
        $this->pipeline = new AssetPipeline($parser, $generator);
    }

    public function testJavascriptIncludeTag()
    {

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