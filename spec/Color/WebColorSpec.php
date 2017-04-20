<?php

namespace spec\Pdf\Color;

use Pdf\Color\WebColor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WebColorSpec extends ObjectBehavior
{
    function it_creates_named_web_colors()
    {
        $this->beConstructedWith('blue');
        $this->__toString()->shouldReturn('blue');
    }

    function it_creates_hex_web_colors()
    {
        $this->beConstructedWith('#0000FF');
        $this->__toString()->shouldReturn('#0000FF');
    }
}
