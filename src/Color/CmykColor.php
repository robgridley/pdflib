<?php

namespace Pdf\Color;

class CmykColor extends Color
{
    /**
     * The color keyword.
     *
     * @var string
     */
    protected $keyword = 'cmyk';

    /**
     * Create a new color instance.
     *
     * @param int $cyan
     * @param int $magenta
     * @param int $yellow
     * @param int $black
     */
    public function __construct($cyan, $magenta, $yellow, $black)
    {
        $this->values = compact('cyan', 'magenta', 'yellow', 'black');
    }
}
