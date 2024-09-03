<?php

namespace Pdf\Pdi;

use Pdf\Handleable;
use Pdf\Pps\PdfBlock;
use Pdf\PdfLibAdapter;
use Pdf\Pps\TextBlock;
use Pdf\Pps\ImageBlock;
use Pdf\Pps\GraphicsBlock;
use Pdf\Pps\BlockCollection;

class PdiPage implements Handleable
{
    /**
     * The adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The document instance.
     *
     * @var PdiDocument
     */
    protected $document;

    /**
     * The page handle.
     *
     * @var int
     */
    protected $handle;

    /**
     * The page number.
     *
     * @var int
     */
    protected $pageNumber;

    /**
     * The block collection instance.
     *
     * @var BlockCollection
     */
    protected $blocks;

    /**
     * Create a new page instance.
     *
     * @param PdfLibAdapter $adapter
     * @param PdiDocument $document
     * @param int $pageNumber
     * @param array $options
     */
    public function __construct(PdfLibAdapter $adapter, PdiDocument $document, $pageNumber, array $options = [])
    {
        $this->adapter = $adapter;

        if (!$this->adapter->isScope(PdfLibAdapter::SCOPE_OBJECT)) {
            $this->handle = $this->adapter->openPdiPage($document, $pageNumber, $options);
        }

        $this->document = $document;
        $this->pageNumber = $pageNumber;
    }

    /**
     * Close the page instance.
     */
    public function close()
    {
        $this->adapter->closePdiPage($this);
    }

    /**
     * Dynamically retrieve the specified property.
     * Camel case is automatically converted to lowercase so your code can be more readable.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($key == 'pageNumber') {
            return $this->pageNumber;
        }

        return $this->getInfo(strtolower($key));
    }

    /**
     * Get the blocks on the page.
     *
     * @return BlockCollection
     */
    public function blocks()
    {
        return $this->blocks ?: $this->blocks = $this->loadBlocks();
    }

    /**
     * Get the specified block.
     *
     * @param string $name
     * @return \Pdf\Pps\Block|null
     */
    public function block($name)
    {
        return $this->blocks()->has($name) ? $this->blocks()->get($name) : null;
    }

    /**
     * Load the blocks from the page.
     *
     * @return BlockCollection
     */
    protected function loadBlocks()
    {
        $blocks = [];

        if ($pcosBlocks = $this->pcos('blocks')) {
            foreach ($pcosBlocks as $pcos) {
                $blocks[] = $this->newBlock($pcos);
            }
        }

        return $this->newBlockCollection($blocks);
    }

    /**
     * Create a new block collection.
     *
     * @param array $blocks
     * @return BlockCollection
     */
    protected function newBlockCollection(array $blocks = [])
    {
        return new BlockCollection($blocks);
    }

    /**
     * Create a new block instance.
     *
     * @param PcosDictionary $properties
     * @return \Pdf\Pps\Block
     */
    protected function newBlock(PcosDictionary $properties)
    {
        $properties = array_change_key_case($properties->toArray());

        switch ($properties['subtype']) {
            case 'PDF':
                return new PdfBlock($this->adapter, $this, $properties);
            case 'Text':
                return new TextBlock($this->adapter, $this, $properties);
            case 'Image':
                return new ImageBlock($this->adapter, $this, $properties);
            case 'Graphics':
                return new GraphicsBlock($this->adapter, $this, $properties);
        }
    }

    /**
     * Get the pCOS value for the specified path.
     *
     * @param mixed $path
     * @return mixed
     */
    public function pcos($path)
    {
        $pageNumber = $this->pageNumber - 1;

        return $this->document->pcos("pages[$pageNumber]/$path");
    }

    /**
     * Get the specified property.
     *
     * @param string $key
     * @param array $options
     * @return float
     */
    public function getInfo($key, array $options = [])
    {
        return $this->adapter->infoPdiPage($this, $key, $options);
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
     * Get the page handle.
     *
     * @return int
     */
    public function getHandle()
    {
        return $this->handle;
    }
}
