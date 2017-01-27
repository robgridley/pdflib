<?php

namespace Pdf\Pps;

class GraphicsBlock extends Block
{
    /**
     * Fill the block with graphics.
     *
     * @param \Pdf\Graphics $contents
     * @param array $options
     */
    public function fill($contents, array $options = [])
    {
        $this->adapter->fillGraphicsBlock($this->page, $this->name, $contents, $options);
    }
}
