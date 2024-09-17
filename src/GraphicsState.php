<?php

namespace Pdf;

class GraphicsState implements Handleable
{
    /**
     * The PDFlib Adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The handle.
     *
     * @var int
     */
    protected $handle;

    /**
     * Create a new graphics state instance.
     *
     * @param PdfLibAdapter $adapter
     * @param array $options
     */
    public function __construct(PdfLibAdapter $adapter, array $options)
    {
        $this->adapter = $adapter;
        $this->handle = $adapter->createGraphicsState($options);
    }

    /**
     * Apply the graphics state.
     *
     * @return void
     */
    public function apply(): void
    {
        $this->adapter->save();
        $this->adapter->setGraphicsState($this);
    }

    /**
     * Restore the previous graphics state.
     *
     * @return void
     */
    public function restore(): void
    {
        $this->adapter->restore();
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
