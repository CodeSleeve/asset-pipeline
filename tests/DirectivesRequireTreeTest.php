<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\Directives\RequireTree;

class DirectivesRequireTreeTest extends PHPUnit_Framework_TestCase
{ 
    public function object($manifestFile = 'application.js')
    {
        return new RequireTree($this->app, $manifestFile);
    }

    public function setUp()
    {
        $this->app = App::make(__DIR__);
        $this->app["path.base"] = __DIR__ . '/fixtures/require_tree';

    }

    public function testThatRelativeDotIsResolvedCorrectly()
    {
        $manifestFile = 'folder2/app1';

        $outcome = $this->object($manifestFile)->process('.');

        $this->assertEquals($outcome, array("app/assets/javascripts/folder2/app1.js", "app/assets/javascripts/folder2/folder3/more depth.js"));
    }
}