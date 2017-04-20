<?php

namespace Pdf\Color;

class WebColor
{
    /**
     * The color value.
     *
     * @var string
     */
    protected $value;

    /**
     * Create a new color instance.
     *
     * @param string $colorNameOrHexValue
     */
    public function __construct($colorNameOrHexValue)
    {
        $this->value = $colorNameOrHexValue;
    }

    /**
     * Convert the instance to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }
}
