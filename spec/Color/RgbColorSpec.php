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
        $this->__toString()->shouldReturn('{rgb 0.3921568627451 0.19607843137255 0.7843137254902}');
    }
}
