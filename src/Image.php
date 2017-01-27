<?php

namespace Pdf;

class Image extends Asset
{
    /**
     * Load the image.
     *
     * @param string $contents
     * @param string $type
     * @param array $options
     */
    protected function load($contents, $type, array $options)
    {
        $path = $path = spl_object_hash($this);

        $this->adapter->createPvf($path, $contents, ['copy']);
        $this->handle = $this->adapter->loadImage($path, $type, $options);
        $this->adapter->deletePvf($path);
    }

    /**
     * Unload the image.
     */
    protected function unload()
    {
        if (!$this->adapter->isScope('object')) {
            $this->adapter->closeImage($this);
        }
    }

    /**
     * Get an image property.
     *
     * @param string $key
     * @param array $options
     * @return mixed
     */
    public function getInfo($key, array $options = [])
    {
        return $this->adapter->infoImage($this, $key, $options);
    }
}
