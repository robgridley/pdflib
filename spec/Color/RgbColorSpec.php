<?php

namespace spec\Pdf\Color;

use Pdf\Color\RgbColor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RgbColorSpec extends ObjectBehavior
{
    function it_creates_rgb_colors()
    {
        $this->beConstructedWith(100, 50, 200);
        $this->__toString()->shouldReturn('{ rgb 100 50 200 }');
    }
}
