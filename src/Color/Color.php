<?php

namespace Pdf\Color;

abstract class Color
{
    /**
     * The color keyword.
     *
     * @var string|null
     */
    protected $keyword;

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
        if (is_null($this->keyword)) {
            return sprintf('{ %s }', implode(' ', $this->values));
        }

        return sprintf('{ %s %s }', $this->keyword, implode(' ', $this->values));
    }
}
