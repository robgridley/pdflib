<?php

namespace spec\Pdf\Color;

use Pdf\Color\LabColor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LabColorSpec extends ObjectBehavior
{
    function it_creates_lab_colors()
    {
        $this->beConstructedWith(100, -50, 50);
        $this->toArray()->shouldReturn(['lab', 100, -50, 50]);
    }

    function it_can_be_converted_to_a_string()
    {
        $this->beConstructedWith(100, -50, 50);
        $this->__toString()->shouldReturn('{lab 100 -50 50}');
    }
}
