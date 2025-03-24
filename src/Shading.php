<?php

namespace Pdf;

class Shading implements Handleable
{
    const TYPE_AXIAL = 'axial';
    const TYPE_RADIAL = 'radial';

    /**
     * The PDFlib adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The PDFlib table handle.
     *
     * @var int
     */
    protected $handle = 0;

    /**
     * Create a new shading instance.
     *
     * @param PdfLibAdapter $adapter
     * @param string $type
     * @param float $x0
     * @param float $y0
     * @param float $x1
     * @param float $y1
     * @param float|null $c1
     * @param float|null $c2
     * @param float|null $c3
     * @param float|null $c4
     * @param array $options
     */
    public function __construct(PdfLibAdapter $adapter, string $type, float $x0, float $y0, float $x1, float $y1, float $c1 = null, float $c2 = null, float $c3 = null, float $c4 = null, array $options = [])
    {
        $this->adapter = $adapter;
        $this->handle = $this->adapter->shading($type, $x0, $y0, $x1, $y1, $c1, $c2, $c3, $c4, $options);
    }

    /**
     * Get the PDFlib handle.
     *
     * @return int
     */
    public function getHandle(): int
    {
        return $this->handle;
    }
}
