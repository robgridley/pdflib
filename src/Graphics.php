<?php

namespace Pdf;

class Graphics extends Asset
{
    /**
     * Load the graphics.
     *
     * @param string $contents
     * @param string $type
     * @param array $options
     */
    protected function load($contents, $type, array $options)
    {
        $path = spl_object_hash($this);

        $this->adapter->createPvf($path, $contents, ['copy']);
        $this->handle = $this->adapter->loadGraphics($path, $type, $options);
        $this->adapter->deletePvf($path);
    }

    /**
     * Unload the graphics.
     */
    protected function unload()
    {
        if ( ! $this->adapter->isScope('object')) {
            $this->adapter->closeGraphics($this);
        }
    }

    /**
     * Get a graphics property.
     *
     * @param string $key
     * @param array $options
     * @return mixed
     */
    public function getInfo($key, array $options = [])
    {
        return $this->adapter->infoGraphics($this, $key, $options);
    }

}
