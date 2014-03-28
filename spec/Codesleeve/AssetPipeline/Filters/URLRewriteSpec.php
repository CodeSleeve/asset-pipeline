<?php

namespace spec\Codesleeve\AssetPipeline\Filters;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class URLRewriteSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Codesleeve\AssetPipeline\Filters\URLRewrite');
    }
}
