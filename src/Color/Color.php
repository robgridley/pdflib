<?php

namespace Pdf\Color;

abstract class Color
{
    /**
     * The color values.
     *
     * @var array
     */
    protected $values = [];

    /**
     * Convert the instance to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('{%s %s}', $this->keyword, implode(' ', $this->values));
    }
}
