<?php

use Codesleeve\AssetPipeline\Test\App;
use Codesleeve\AssetPipeline\AssetFilters;
use Codesleeve\AssetPipeline\FileTypeFilterProvider;

class FileTypeFilterProviderTest extends PHPUnit_Framework_TestCase
{ 
	public function object()
	{
		return new FileTypeFilterProvider($this->app);
	}

	public function setUp()
	{
	 	$this->app = App::make(__DIR__);
	}
	
	public function testGetTypeFilter()
	{
		$expected = 'Codesleeve\AssetPipeline\Filters\Impl\JavascriptsTypeFilter';
		$outcome = $this->object()->getTypeFilter(AssetFilters::JAVASCRIPTS);
		$this->assertInstanceOf($expected, $outcome);
	}
	
	public function testMergingCustomFilters()
	{	   
		$this->app['config']->set(
			'filtertypes',
			array(
				'javascripts' => array(),		
				'stylesheets' => array('.xslt'),
				'others' => array(),
			)
		);
		$outcome = $this->object()->getTypeFilter(AssetFilters::STYLESHEETS);
		$this->assertTrue($outcome->isOfType('myfile.min.css'));
		$this->assertTrue($outcome->isOfType('myfile.xslt'));
	}
}