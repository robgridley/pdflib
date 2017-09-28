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
        return $this->totalPages;
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

        return array_key_exists($pageNumber, $this->pages) ?
            $this->pages[$pageNumber] :
            $this->pages[$pageNumber] = $this->newPdiPage($pageNumber, $options);
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
    }
}
