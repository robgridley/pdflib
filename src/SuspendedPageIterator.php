<?php

namespace Pdf;

use Iterator;

class SuspendedPageIterator implements Iterator
{
    /**
     * The suspended pages.
     *
     * @var array
     */
    protected $pages;

    /**
     * The PDF builder.
     *
     * @var PdfBuilder
     */
    protected $builder;

    /**
     * Create a new iterator instance.
     *
     * @param array $pages
     * @param PdfBuilder $builder
     */
    public function __construct(array $pages, PdfBuilder $builder)
    {
        $this->pages = $pages;
        $this->builder = $builder;
    }

    /**
     * Resume the current suspended page.
     *
     * @return PdfBuilder
     */
    public function current()
    {
        $this->builder->resumePage($this->key());

        return $this->builder;
    }

    /**
     * Move forward to the next suspended page.
     */
    public function next()
    {
        next($this->pages);
    }

    /**
     * Return the key of the current suspended page.
     *
     * @return int|null
     */
    public function key()
    {
        $key = current($this->pages);

        return $key === false ? null : $key;
    }

    /**
     * Checks if current suspended page is valid.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->key() !== null;
    }

    /**
     * Rewind to the first suspended page.
     */
    public function rewind()
    {
        reset($this->pages);
    }
}
