<?php

namespace Pdf\Color;

class WebColor extends Color
{
    /**
     * Create a new color instance.
     *
     * @param string $colorNameOrHexValue
     */
    public function __construct($colorNameOrHexValue)
    {
        $this->values = compact('colorNameOrHexValue');
    }
}
