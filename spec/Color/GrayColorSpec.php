<?php

namespace spec\Pdf\Color;

use Pdf\Color\GrayColor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrayColorSpec extends ObjectBehavior
{
    function it_creates_gray_colors()
    {
        $this->beConstructedWith(0.5);
        $this->toArray()->shouldReturn(['gray', 0.5]);
    }
}
