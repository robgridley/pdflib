<?php

namespace Pdf\Pps;

class PdfBlock extends Block
{
    /**
     * Fill the block with a Pdf page.
     *
     * @param \Pdf\Pdi\PdiPage $contents
     * @param array $options
     */
    public function fill($contents, array $options = [])
    {
        $this->adapter->fillPdfBlock($this->page, $this->name, $contents, $options);
    }
}
