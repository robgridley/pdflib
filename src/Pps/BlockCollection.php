<?php

namespace Pdf\Pps;

use Countable;
use ArrayAccess;
use ArrayIterator;
use LogicException;
use IteratorAggregate;
use InvalidArgumentException;

class BlockCollection implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * The block instances.
     *
     * @var Block[]
     */
    protected $blocks = [];

    /**
     * Create a new block collection instance.
     *
     * @param Block[] $blocks
     */
    public function __construct(array $blocks)
    {
        foreach ($blocks as $block) {
            if (!$block instanceof Block) {
                throw new InvalidArgumentException('The first argument must be an array of Block instances');
            }

            $this->blocks[$block->name] = $block;
        }
    }

    /**
     * Count the number of blocks in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->blocks);
    }

    /**
     * Automatically fill the blocks with the specified source.
     *
     * @param array|\ArrayAccess $source
     */
    public function fill($source)
    {
        foreach ($this->blocks as $key => $block) {
            if (isset($source[$key])) {
                $block->fill($source[$key]);
            }
        }
    }

    /**
     * Get the specified block.
     *
     * @param string $name
     * @return Block
     */
    public function get($name)
    {
        return $this->blocks[$name];
    }

    /**
     * Get an iterator for the blocks.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->blocks);
    }

    /**
     * Determine if the specified block exists in the collection.
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->blocks);
    }

    /**
     * Determine if the block collection is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return count($this->blocks) == 0;
    }

    /**
     * Get the keys (names) for the blocks.
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->blocks);
    }

    /**
     * Determine if the specified offset exists.
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Get the value for the specified offset.
     *
     * @param string $offset
     * @return Block
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Required by the ArrayAccess interface.
     *
     * @param string $key
     * @param mixed $value
     * @throws LogicException when called.
     */
    public function offsetSet($key, $value)
    {
        throw new LogicException('BlockCollection is immutable');
    }

    /**
     * Required by the ArrayAccess interface.
     *
     * @param string $key
     * @throws LogicException when called.
     */
    public function offsetUnset($key)
    {
        throw new LogicException('BlockCollection is immutable');
    }

    /**
     * Filter blocks by the specified type.
     *
     * @param string $type
     * @return BlockCollection
     */
    public function only($type)
    {
        return $this->filter(function ($block) use ($type) {
            return is_a($block, $type);
        });
    }

    /**
     * Filter blocks using the specified function.
     *
     * @param callable $callback
     * @return BlockCollection
     */
    public function filter(callable $callback)
    {
        $blocks = array_filter($this->blocks, $callback);

        return $this->newCollection($blocks);
    }

    /**
     * Convert the instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->blocks;
    }

    /**
     * Create a new collection instance.
     *
     * @param array $blocks
     * @return static
     */
    protected function newCollection(array $blocks)
    {
        return new static($blocks);
    }
}
