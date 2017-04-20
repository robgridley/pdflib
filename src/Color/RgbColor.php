<?php

namespace Pdf\Color;

class RgbColor extends Color
{
    /**
     * The color keyword.
     *
     * @var string
     */
    protected $keyword = 'rgb';

    /**
     * Create a new color instance.
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function __construct($red, $green, $blue)
    {
        $red /= 255;
        $green /= 255;
        $blue /= 255;

        $this->values = compact('red', 'green', 'blue');
    }
}
