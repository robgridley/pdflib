<?php

namespace Pdf\Pps;

class ImageBlock extends Block
{
    /**
     * Fill the block with an image.
     *
     * @param \Pdf\Image $contents
     * @param array $options
     */
    public function fill($contents, array $options = [])
    {
        $options = array_merge($this->fillOptions, $options);

        $this->adapter->fillImageBlock($this->page, $this->name, $contents, $options);
    }
}
