<?php

namespace spec\Codesleeve\AssetPipeline\Filters;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SassFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Codesleeve\AssetPipeline\Filters\SassFilter');
    }
}
