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
     * The column position.
     *
     * @var int
     */
    protected $column = 1;

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
        $this->column = 1;
    }

    /**
     * Add a column to the current row.
     *
     * @param string $text
     * @param array $options
     */
    public function addColumn($text, array $options)
    {
        $options = array_merge($this->cellOptions, $options);

        $this->handle = $this->adapter->addTableCell($this, $this->column++, $this->row, $text, $options);
    }

    /**
     * Add a column to the current row.
     *
     * @param string $text
     * @param array $textOptions
     * @param array $options
     */
    public function addTextlineColumn($text, $textOptions = [], $options = [])
    {
        $options['fitTextline'] = array_merge($this->textOptions, $textOptions);

        return $this->addColumn($text, $options);
    }

    /**
     * Set the default font.
     *
     * @param Font $font
     * @param int $size
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
     * Get PDFlib handle for the instance.
     *
     * @return int
     */
    public function getHandle()
    {
        return $this->handle;
    }
}
