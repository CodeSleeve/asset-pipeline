<?php

namespace spec\Codesleeve\AssetPipeline;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AssetPipelineServiceProviderSpec extends ObjectBehavior
{
    function it_is_initializable(\Illuminate\Container\Container $app)
    {
    	$this->beConstructedWith($app);
        $this->shouldHaveType('Codesleeve\AssetPipeline\AssetPipelineServiceProvider');
    }
}
