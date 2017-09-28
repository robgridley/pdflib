<?php

namespace Pdf;

abstract class Asset implements Handleable
{
    /**
     * The PDFlib Adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The PDFlib handle.
     *
     * @var int
     */
    protected $handle;

    /**
     * Create an instance.
     *
     * @param PdfLibAdapter $adapter
     * @param string $contents
     * @param string $type
     * @param array $options
     */
    public function __construct(PdfLibAdapter $adapter, $contents, $type = null, array $options = [])
    {
        $this->adapter = $adapter;
        $this->load($contents, $type, $options);
    }

    /**
     * Destroy an instance.
     */
    public function __destruct()
    {
        $this->unload();
    }

    /**
     * Load the asset.
     *
     * @param string $contents
     * @param string $type
     * @param array $options
     */
    abstract protected function load($contents, $type, array $options);

    /**
     * Unload the asset.
     */
    abstract protected function unload();

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
     * Get a property.
     *
     * @param string $key
     * @param array $options
     * @return mixed
     */
    abstract public function getInfo($key, array $options = []);

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
     * Get the instance as a PDFlib handle.
     *
     * @return int
     */
    public function getHandle()
    {
        return $this->handle;
    }
}
