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

    public function testBlank() {}
//     public function testErrorsIfManifestFileDoesntExist()
//     {
//         $this->setExpectedException('InvalidArgumentException');
//     	$outcome = $this->object()->javascriptIncludeTag('manifest_does_not_exist');
//     }

//     public function testDefaultJavascriptIncludeTagsProd()
//     {
//         $this->app['env'] = 'production';
//         $outcome = $this->object()->javascriptIncludeTag();

//         $this->assertEquals($outcome, '<script src="application.js"></script>');
//     }

//     public function testJavascriptIncludeTagsProdWithManifestAndAttributes()
//     {
//         $this->app['env'] = 'production';
//         $outcome = $this->object()->javascriptIncludeTag('application', array('defer' => 'defer', 'class' => 'something'));
//         $this->assertEquals($outcome, '<script src="application.js" defer="defer" class="something"></script>');
//     }

//     public function testDefaultJavascriptIncludeTagsDev()
//     {
//         $outcome = $this->object()->javascriptIncludeTag();
//         print_r($outcome);
// //        $this->assertEquals($outcome, '<script src="application.js"></script>');
//     }



}    