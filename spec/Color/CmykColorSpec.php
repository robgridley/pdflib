<?php

namespace spec\Pdf\Color;

use Pdf\Color\CmykColor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CmykColorSpec extends ObjectBehavior
{
    function it_creates_cmyk_colors()
    {
        $this->beConstructedWith(30, 20, 10, 40);
        $this->__toString()->shouldReturn('{ cmyk 30 20 10 40 }');
    }
}
