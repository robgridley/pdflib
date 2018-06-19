<?php

namespace Pdf\Color;

use Pdf\PdfLibAdapter;

class SpotColor extends Color
{
    /**
     * The colour keyword.
     *
     * @var string
     */
    protected $keyword = 'spot';

    /**
     * Create a new colour instance.
     *
     * @param PdfLibAdapter $adapter
     * @param string $name
     * @param float $tint
     */
    public function __construct(PdfLibAdapter $adapter, $name, $tint)
    {
        $handle = $adapter->makeSpotColor($name);

        $this->values = compact('handle', 'tint');
    }
}
