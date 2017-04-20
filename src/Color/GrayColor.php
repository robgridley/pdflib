<?php

namespace Pdf\Color;

class GrayColor extends Color
{
    /**
     * The color keyword.
     *
     * @var string
     */
    protected $keyword = 'gray';

    /**
     * Create a new color instance.
     *
     * @param float $value
     */
    public function __construct($value)
    {
        $this->values = compact('value');
    }
}
