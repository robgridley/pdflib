<?php

namespace Pdf\Color;

class LabColor extends Color
{
    /**
     * The color keyword.
     *
     * @var string
     */
    protected $keyword = 'lab';

    /**
     * Create a new color instance.
     *
     * @param int $lightness
     * @param int $a
     * @param int $b
     */
    public function __construct($lightness, $a, $b)
    {
        $this->values = compact('lightness', 'a', 'b');
    }
}
