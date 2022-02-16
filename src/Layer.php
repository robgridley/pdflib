<?php

namespace Pdf;

class Layer implements Handleable
{
    const DEPEND_TYPE_GROUP_ALL_ON = 'GroupAllOn';
    const DEPEND_TYPE_GROUP_ANY_ON = 'GroupAnyOn';
    const DEPEND_TYPE_GROUP_ALL_OFF = 'GroupAllOff';
    const DEPEND_TYPE_GROUP_ANY_OFF = 'GroupAnyOff';
    const DEPEND_TYPE_LOCK = 'Lock';
    const DEPEND_TYPE_PARENT = 'Parent';
    const DEPEND_TYPE_RADIO_BTN = 'Radiobtn';
    const DEPEND_TYPE_TITLE = 'Title';

    /**
     * The PDFlib adapter.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The PDFlib layer handle.
     *
     * @var int
     */
    protected $handle;

    /**
     * The layer name.
     *
     * @var string
     */
    protected $name;

    /**
     * Create a new layer instance.
     *
     * @param PdfLibAdapter $adapter
     * @param string $name
     * @param array $options
     */
    public function __construct(PdfLibAdapter $adapter, string $name, array $options = [])
    {
        $this->adapter = $adapter;
        $this->name = $name;
        $this->handle = $this->adapter->defineLayer($name, $options);
    }

    /**
     * Get the layer name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Start the layer.
     */
    public function begin(): void
    {
        $this->adapter->beginLayer($this);
    }

    /**
     * Deactivate all active layers.
     */
    public function end(): void
    {
        $this->adapter->endLayer();
    }

    /**
     * Get the instance as a PDFlib handle.
     *
     * @return int
     */
    public function getHandle(): int
    {
        return $this->handle;
    }
}
