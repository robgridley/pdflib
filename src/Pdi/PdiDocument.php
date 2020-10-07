<?php

namespace Pdf\Pdi;

use Iterator;
use Countable;
use Pdf\Handleable;
use Pdf\PdfLibAdapter;
use OutOfBoundsException;

class PdiDocument implements Handleable, Iterator, Countable
{
    /**
     * The PDFlib adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The document handle.
     *
     * @var int
     */
    protected $handle;

    /**
     * The read (cached) pages.
     *
     * @var PdiPage[]
     */
    protected $pages = [];

    /**
     * The default page options.
     *
     * @var array
     */
    protected $pageOptions = [];

    /**
     * The current page (for iteration).
     *
     * @var int
     */
    protected $currentPage = 1;

    /**
     * The total number of pages in the document.
     *
     * @var int
     */
    protected $totalPages = 0;

    /**
     * The pages which should not be included when iterating.
     *
     * @var array
     */
    protected $ignoredPages = [];

    /**
     * The pCOS instance.
     *
     * @var Pcos
     */
    protected $pcos;

    /**
     * Create a new document instance.
     *
     * @param PdfLibAdapter $adapter
     * @param string $contents
     * @param array $options
     */
    public function __construct(PdfLibAdapter $adapter, $contents, array $options = [])
    {
        $this->adapter = $adapter;
        $this->load($contents, $options);
        $this->pcos = new Pcos($this->adapter, $this);
        $this->totalPages = count($this->pcos('pages'));
    }

    /**
     * Destroy the instance.
     */
    public function __destruct()
    {
        $this->unload();
    }

    /**
     * Load the specified document.
     *
     * @param string $contents
     * @param array $options
     */
    protected function load($contents, array $options)
    {
        $path = spl_object_hash($this);

        $this->adapter->createPvf($path, $contents, ['copy']);
        $this->handle = $this->adapter->openPdiDocument($path, $options);
        $this->adapter->deletePvf($path);
    }

    /**
     * Unload the document.
     */
    protected function unload()
    {
        $this->adapter->closePdiDocument($this);
    }

    /**
     * The total number of pages for iteration.
     *
     * @return int
     */
    public function count()
    {
        return count(array_diff(range(1, $this->totalPages), $this->ignoredPages));
    }

    /**
     * Get the specified page.
     *
     * @param int $pageNumber
     * @param array $options
     * @return PdiPage
     */
    public function page($pageNumber, array $options = [])
    {
        if (!$this->hasPage($pageNumber)) {
            throw new OutOfBoundsException('Page does not exist');
        }

        if (!array_key_exists($pageNumber, $this->pages)) {
            $this->pages[$pageNumber] = $this->newPdiPage($pageNumber, array_merge($this->pageOptions, $options));
        }

        return $this->pages[$pageNumber];
    }

    /**
     * Determine if the document contains the specified page.
     *
     * @param int $pageNumber
     * @return bool
     */
    public function hasPage($pageNumber)
    {
        return $pageNumber >= 1 && $pageNumber <= $this->totalPages;
    }

    /**
     * Get the pCOS value for the specified path.
     *
     * @param mixed $path
     * @return mixed
     */
    public function pcos($path)
    {
        return $this->pcos->getValue($path);
    }

    /**
     * Get the specified page.
     *
     * @param int $pageNumber
     * @param array $options
     * @return PdiPage
     */
    protected function newPdiPage($pageNumber, array $options)
    {
        return new PdiPage($this->adapter, $this, $pageNumber, $options);
    }

    /**
     * Get the adapter instance.
     *
     * @return PdfLibAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Get the document handle.
     *
     * @return int
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Set the default page options.
     *
     * @param array $options
     */
    public function setPageOptions(array $options)
    {
        $this->pageOptions = $options;
    }

    /**
     * Set the pages which should be ignored when iterating.
     *
     * @param array $pages
     */
    public function setIgnoredPages(array $pages)
    {
        $this->ignoredPages = $pages;
    }

    /**
     * Get the current page.
     *
     * @return PdiPage
     */
    public function current()
    {
        return $this->page($this->currentPage);
    }

    /**
     * Advance to the next page and return it.
     *
     * @return PdiPage|false
     */
    public function next()
    {
        $this->currentPage++;

        $this->skipIgnoredPages();

        if (!$this->valid()) {
            return false;
        }

        return $this->page($this->currentPage);
    }

    /**
     * Get the current page number.
     *
     * @return int
     */
    public function key()
    {
        return $this->currentPage;
    }

    /**
     * Determine if the current page exists.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->hasPage($this->currentPage);
    }

    /**
     * Go to the first page.
     */
    public function rewind()
    {
        $this->currentPage = 1;

        $this->skipIgnoredPages();
    }

    /**
     * Skip over any ignored pages.
     */
    protected function skipIgnoredPages()
    {
        while (in_array($this->currentPage, $this->ignoredPages)) {
            $this->currentPage++;
        }
    }
}
