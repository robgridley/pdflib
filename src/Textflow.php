<?php

namespace Pdf;

class Textflow implements Handleable
{
    /**
     * The PDFlib adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The PDFlib textflow handle.
     *
     * @var int
     */
    protected $handle = 0;

    /**
     * Create a new textflow instance.
     *
     * @param PdfLibAdapter $adapter
     * @param Font $font
     * @param float $size
     * @param string $text
     * @param array $options
     */
    public function __construct(PdfLibAdapter $adapter, Font $font, $size, $text = null, array $options = [])
    {
        $options['font'] = $font;
        $options['fontSize'] = $size;

        $this->handle = $adapter->createTextflow($text, $options);
        $this->adapter = $adapter;
    }

    /**
     * Append text to the textflow.
     *
     * @param string $text
     * @param array $options
     * @return $this
     */
    public function append($text, array $options = [])
    {
        $this->handle = $this->adapter->addTextflow($this->handle, $text, $options);

        return $this;
    }

    /**
     * Dynamically access properties.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getInfo($key);
    }

    /**
     * Get detailed information about the textflow.
     *
     * @param string $key
     * @return mixed
     */
    public function getInfo($key)
    {
        return $this->adapter->infoTextflow($this, $key);
    }

    /**
     * Get PDFlib handle for the instance.
     *
     * @return int
     */
    public function getHandle()
    {
        return $this->handle;
    }
}
