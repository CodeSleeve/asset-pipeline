<?php

namespace spec\Codesleeve\AssetPipeline;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AssetSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Codesleeve\AssetPipeline\Asset');
    }
}
