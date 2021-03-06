<?php

namespace Pdf\Pps;

use Pdf\Pdi\PdiPage;
use Pdf\PdfLibAdapter;
use InvalidArgumentException;

abstract class Block
{
    /**
     * The PDFlib Adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The page the block belongs to.
     *
     * @var PdiPage
     */
    protected $page;

    /**
     * Block properties.
     *
     * @var array
     */
    protected $properties = [];

    /**
     * The default fill options.
     *
     * @var array
     */
    protected $fillOptions = [];

    /**
     * Create a new block instance.
     *
     * @param PdfLibAdapter $adapter
     * @param PdiPage $page
     * @param array $properties
     */
    public function __construct(PdfLibAdapter $adapter, PdiPage $page, array $properties)
    {
        $this->adapter = $adapter;
        $this->page = $page;
        $this->setProperties($properties);
    }

    /**
     * Populate the block properties.
     *
     * @param array $properties
     */
    protected function setProperties(array $properties)
    {
        if (!isset($properties['name'], $properties['subtype'])) {
            throw new InvalidArgumentException('The properties array must contain name and subtype keys.');
        }

        $this->properties = $properties;
    }

    /**
     * Dynamically access block properties.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return array_key_exists($key, $this->properties) ? $this->properties[$key] : null;
    }

    /**
     * Dynamically determine if a block property is set.
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->properties[$key]);
    }

    /**
     * Fill the block.
     *
     * @param mixed $contents
     * @param array $options
     */
    abstract public function fill($contents, array $options = []);

    /**
     * Set the specified default fill option.
     *
     * @param string $key
     * @param $value
     */
    public function setFillOption(string $key, $value)
    {
        $this->fillOptions[$key] = $value;
    }

    /**
     * Set the default fill options.
     *
     * @param array $options
     */
    public function setFillOptions(array $options)
    {
        $this->fillOptions = $options;
    }

    /**
     * Get the width of the block.
     *
     * @return float
     */
    public function getWidth()
    {
        return $this->properties['rect'][2] - $this->properties['rect'][0];
    }

    /**
     * Get the height of the block.
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->properties['rect'][3] - $this->properties['rect'][1];
    }

    /**
     * Get the page the block belongs to.
     *
     * @return PdiPage
     */
    public function getPage(): PdiPage
    {
        return $this->page;
    }

    /**
     * Get the adapter instance.
     *
     * @return PdfLibAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
