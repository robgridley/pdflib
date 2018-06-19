<?php

namespace spec\Pdf\Color;

use Pdf\Color\RgbColor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RgbColorSpec extends ObjectBehavior
{
    function it_creates_rgb_colors()
    {
        $this->beConstructedWith(51, 102, 153);
        $this->toArray()->shouldReturn(['rgb', 0.2, 0.4, 0.6]);
    }

    function it_can_be_converted_to_a_string()
    {
        $this->beConstructedWith(51, 102, 153);
        $this->__toString()->shouldReturn('{rgb 0.2 0.4 0.6}');
    }
}
