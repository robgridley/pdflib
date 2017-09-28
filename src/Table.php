<?php

namespace Pdf;

use Closure;

class Table implements Handleable
{
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
     * The row position.
     *
     * @var int
     */
    protected $row = 1;

    /**
     * The default cell options.
     *
     * @var array
     */
    protected $cellOptions = [];

    /**
     * The default text options.
     *
     * @var array
     */
    protected $textOptions = [];

    /**
     * Create a new table instance.
     *
     * @param PdfLibAdapter $adapter
     */
    public function __construct(PdfLibAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Dynamically access properties.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getInfo($key);
    }

    /**
     * Get detailed information about the table.
     *
     * @param string $key
     * @return mixed
     */
    public function getInfo($key)
    {
        return $this->adapter->infoTable($this, $key);
    }

    /**
     * Add a row.
     *
     * @param Closure $callback
     */
    public function addRow(Closure $callback)
    {
        $callback($this);

        $this->row++;
    }

    /**
     * Add a column to the current row.
     *
     * @param int $column
     * @param mixed $contents
     * @param array $contentOptions
     * @param array $options
     */
    public function addColumn($column, $contents, array $contentOptions = [], array $options = [])
    {
        return $this->addCell($column, $this->row, $contents, $contentOptions, $options);
    }

    /**
     * Add a cell.
     *
     * @param int $column
     * @param int $row
     * @param mixed $contents
     * @param array $contentOptions
     * @param array $options
     */
    public function addCell($column, $row, $contents, array $contentOptions = [], array $options = [])
    {
        if ($contents instanceof Image) {
            $options['image'] = $contents;
            $options['fitImage'] = $contentOptions;
            $contents = null;
        } elseif ($contents instanceof Graphics) {
            $options['graphics'] = $contents;
            $options['fitGraphics'] = $contentOptions;
            $contents = null;
        } elseif($contents instanceof Textflow) {
            $options['textflow'] = $contents;
            $options['fitTextflow'] = $contentOptions;
            $contents = null;
        } else {
            $options['fitTextline'] = array_merge($this->textOptions, $contentOptions);
        }

        $options = array_merge($this->cellOptions, $options);

        $this->handle = $this->adapter->addTableCell($this, $column, $row, $contents, $options);
    }

    /**
     * Set the default font.
     *
     * @param Font $font
     * @param float $size
     */
    public function setFont(Font $font, $size)
    {
        $this->textOptions['font'] = $font;
        $this->textOptions['fontSize'] = $size;
    }

    /**
     * Set the default row height.
     *
     * @param float $height
     */
    public function setRowHeight($height)
    {
        $this->cellOptions['rowHeight'] = $height;
    }

    /**
     * Set the default cell margin.
     *
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @param float $left
     */
    public function setCellMargin($top, $right = null, $bottom = null, $left = null)
    {
        $this->cellOptions['marginTop'] = $top;
        $this->cellOptions['marginRight'] = is_null($right) ? $right = $top : $right;
        $this->cellOptions['marginBottom'] = is_null($bottom) ? $top : $bottom;
        $this->cellOptions['marginLeft'] = is_null($left) ? $right : $left;
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

    /**
     * Get PDFlib handle for the instance.
     *
     * @return int
     */
    public function getHandle()
    {
        return $this->handle;
    }
}
