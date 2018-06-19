<?php

namespace Pdf\Color;

use Pdf\Arrayable;

abstract class Color implements Arrayable
{
    /**
     * The color values.
     *
     * @var array
     */
    protected $values = [];

    /**
     * Convert the instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge([$this->keyword], array_values($this->values));
    }

    /**
     * Convert the instance to a string for use in inline option lists.
     *
     * @return string
     */
    public function __toString()
    {
        return '{' . implode(' ', $this->toArray()) . '}';
    }
}
