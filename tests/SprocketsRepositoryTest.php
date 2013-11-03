<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\SprocketsRepository;

class SprocketsRepositoryTest extends PHPUnit_Framework_TestCase
{ 
    public function object()
    {
        return new SprocketsRepository($this->app);
    }

    public function setUp()
    {
     	$this->app = App::make(__DIR__);
    }

    public function testJavascripts()
    {
    	$outcome = $this->object()->javascripts('application');
    	$this->assertContains('//= require jquery', $outcome);
    }

    public function testJavascriptsProd()
    {
        $this->app['env'] = 'production';
        $outcome = $this->object()->javascripts('application');
        $this->assertEquals('jquery.js;', $outcome);
    }

    public function testStylesheets()
    {
    	$outcome = $this->object()->stylesheets('application');
		$this->assertContains('*= require foobar2', $outcome);
    }

    public function testStylesheetsProd()
    {
    	$this->app['env'] = 'production';
    	$outcome = $this->object()->stylesheets('application');
        $this->assertEquals('.foobar2{color:black}', $outcome);
    }
    
    public function testStylesheetsProdUrlFiltered()
    {
    	$this->app['env'] = 'production';
        $this->app['config']->set(
            'paths',
            array(
                'app/assets/stylesheets',
                'foo/assets' => 'stylesheets,javascripts'
            )
        );
        $expected = ".filterurl{background:transparent url(/assets/thirdparty/images/background.png) no-repeat 50% 50%}@font-face{src:url(/assets/thirdparty/fonts/icons.eot);src:url(/assets/thirdparty/fonts/icons.eot?#iefix) format('embedded-opentype'), url(/assets/thirdparty/fonts/icons.woff) format('woff'), url(/assets/thirdparty/fonts/icons.ttf) format('truetype'), url(/assets/thirdparty/fonts/icons.svg#icons) format('svg')}";
    	$outcome = $this->object()->stylesheets('application1');
        $this->assertEquals($expected, $outcome);
    }

    public function testIsJavascript()
    {
        $outcome = $this->object()->isJavascript('application.js');
        $this->assertTrue($outcome);
    }

    public function testIsJavascriptOnInvalidAsset()
    {
        $outcome = $this->object()->isJavascript('application.js1');
        $this->assertFalse($outcome);
    }

    public function testIsJavascriptWthPath()
    {
        $outcome = $this->object()->isJavascript('foobar/foobar.js');
        $this->assertTrue($outcome);
    }

    public function testIsStylesheet()
    {
        $outcome = $this->object()->isStylesheet('application.css');
        $this->assertTrue($outcome);
    }

    public function testIsStylesheetOnInvalidAsset()
    {
        $outcome = $this->object()->isStylesheet('application.css1');
        $this->assertFalse($outcome);        
    }

    public function testStylesheetWithPath()
    {
        $outcome = $this->object()->isStylesheet('foobar\\foobar.css');
        $this->assertTrue($outcome);
    }

}