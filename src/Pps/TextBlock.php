<?php

namespace Pdf\Pps;

class TextBlock extends Block
{
    /**
     * Fill the block with text.
     *
     * @param string $contents
     * @param array $options
     */
    public function fill($contents, array $options = ['embedding'])
    {
        $options = array_merge($this->fillOptions, $options);

        $this->adapter->fillTextBlock($this->page, $this->name, $contents, $options);
    }
}
