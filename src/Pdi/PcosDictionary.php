<?php

namespace Pdf\Pdi;

use Iterator;
use Countable;
use ArrayAccess;
use LogicException;

class PcosDictionary implements Iterator, Countable, ArrayAccess
{
    /**
     * The parent pCOS query result.
     *
     * @var Pcos
     */
    protected $pcos;

    /**
     * The pCOS path.
     *
     * @var string
     */
    protected $path;

    /**
     * The current index (for iteration).
     *
     * @var int
     */
    protected $index = 0;

    /**
     * Create a new instance.
     *
     * @param Pcos $pcos
     * @param string $path
     */
    function __construct(Pcos $pcos, $path)
    {
        $this->pcos = $pcos;
        $this->path = $path;
    }

    /**
     * Get the current element.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->pcos->getValue("{$this->getCurrentPath()}.val");
    }

    /**
     * Advance to the next element.
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Get the key for the current element.
     *
     * @return string
     */
    public function key()
    {
        return $this->pcos->getStringValue("{$this->getCurrentPath()}.key");
    }

    /**
     * Determine if the current element exists.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->index < $this->count();
    }

    /**
     * Go to the first element.
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Get the number of elements.
     *
     * @return int
     */
    public function count()
    {
        return $this->pcos->getLength($this->path);
    }

    /**
     * Determine if the specified element exists.
     *
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return !is_null($this->pcos->getType($this->getPath($key)));
    }

    /**
     * Get the specified element.
     *
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->pcos->getValue($this->getPath($key));
    }

    /**
     * Required by the ArrayAccess interface.
     *
     * @param mixed $key
     * @param mixed $value
     * @throws LogicException when called.
     */
    public function offsetSet($key, $value)
    {
        throw new LogicException('PcosDictionary is immutable');
    }

    /**
     * Required by the ArrayAccess interface.
     *
     * @param mixed $key
     * @throws LogicException when called.
     */
    public function offsetUnset($key)
    {
        throw new LogicException('PcosDictionary is immutable');
    }

    /**
     * Get the path for the specified key.
     *
     * @param mixed $key
     * @return string
     */
    protected function getPath($key)
    {
        if (is_int($key)) {
            return "$this->path[$key]";
        }

        return "$this->path/$key";
    }

    /**
     * Get the current path.
     *
     * @return string
     */
    protected function getCurrentPath()
    {
        return $this->getPath($this->index);
    }

    /**
     * Convert the instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this);
    }
}
