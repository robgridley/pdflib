<?php

namespace Pdf;

use ArrayAccess;
use LogicException;
use Pdf\Pdi\PdiPage;
use Pdf\Pdi\PdiDocument;

class PdfBuilder implements ArrayAccess
{
    /**
     * The adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * Create a new instance.
     *
     * @param PdfLibAdapter $adapter
     * @param array $options
     */
    public function __construct(PdfLibAdapter $adapter, array $options = [])
    {
        $this->adapter = $adapter;

        $this->adapter->beginDocument(null, $options);
    }

    /**
     * Add a page to the current document.
     *
     * @param int $width
     * @param int $height
     * @param array $options
     */
    public function addPage($width = 0, $height = 0, array $options = [])
    {
        if ($this->adapter->isScope('page')) {
            $this->adapter->endPage();
        }

        $this->adapter->beginPage($width, $height, $options);
    }

    /**
     * Load an image.
     *
     * @param string $contents
     * @param string $type
     * @param array $options
     * @return Image
     */
    public function loadImage($contents, $type = null, array $options = [])
    {
        return new Image($this->adapter, $contents, $type, $options);
    }

    /**
     * Load a SVG file.
     *
     * @param string $contents
     * @param string $type
     * @param array $options
     * @return Graphics
     */
    public function loadGraphics($contents, $type = 'auto', array $options = [])
    {
        return new Graphics($this->adapter, $contents, $type, $options);
    }

    /**
     * Import a document.
     *
     * @param string $contents
     * @param array $options
     * @return PdiDocument
     */
    public function import($contents, array $options = [])
    {
        return new PdiDocument($this->adapter, $contents, $options);
    }

    /**
     * Place an imported page onto the current page.
     *
     * @param PdiPage $page
     * @param int $x
     * @param int $y
     * @param array $options
     */
    public function placePage(PdiPage $page, $x = 0, $y = 0, array $options = [])
    {
        $this->adapter->fitPdiPage($page, $x, $y, $options);
    }

    /**
     * Place graphics into the page at the specified coordinates.
     *
     * @param Graphics $graphic
     * @param float $x
     * @param float $y
     * @param float $w
     * @param float $h
     * @param array $options
     */
    public function insertGraphics(Graphics $graphic, $x = 0.0, $y = 0.0, $w = 0.0, $h = 0.0, array $options = [])
    {
        $options['boxSize'] = [$w, $h];

        $this->adapter->fitGraphics($graphic, $x, $y, $options);
    }

    /**
     * Render the document.
     *
     * @return string
     */
    public function render()
    {
        if ($this->adapter->isScope('page')) {
            $this->adapter->endPage();
        }

        $this->adapter->endDocument();

        return $this->adapter->getBuffer();
    }

    /**
     * Get the Internet media type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return 'application/pdf';
    }

    /**
     * Get the adapter instance.
     *
     * @return PdfLibAdapter
     */
    public function adapter()
    {
        return $this->adapter;
    }

    /**
     * Determine if a global option exists.
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        $this->adapter->optionExists($key);
    }

    /**
     * Get a global option.
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->adapter->getOption($key);
    }

    /**
     * Set a global option.
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->adapter->setOption($key, $value);
    }

    /**
     * Required by the ArrayAccess interface.
     *
     * @param string $key
     * @throws LogicException when called.
     */
    public function offsetUnset($key)
    {
        throw new LogicException('PDF options cannot be unset');
    }

    /**
     * Convert the instance to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
