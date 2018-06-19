<?php

namespace Pdf;

class Font implements Handleable
{
    /**
     * The PDFlib adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The PDFlib font handle.
     *
     * @var int
     */
    protected $handle;

    /**
     * Create a new font instance.
     *
     * @param PdfLibAdapter $adapter
     * @param $name
     * @param $encoding
     * @param $options
     */
    public function __construct(PdfLibAdapter $adapter, $name, $encoding, $options)
    {
        $this->handle = $adapter->loadFont($name, $encoding, $options);
        $this->adapter = $adapter;
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
     * Get detailed information about the font.
     *
     * @param string $key
     * @param array $options
     * @return mixed
     */
    public function getInfo($key, array $options = [])
    {
        return $this->adapter->infoFont($this, $key, $options);
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
     * Get PDFlib handle for the instance.
     *
     * @return int
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Convert the instance to a string for use in inline option lists.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getHandle();
    }
}
