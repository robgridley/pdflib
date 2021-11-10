<?php

namespace Pdf;

use Pdf\Color\Color;

class Drawing
{
    /**
     * The PDFlib adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * Create a new drawing instance.
     *
     * @param PdfLibAdapter $adapter
     */
    public function __construct(PdfLibAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Set the stroke colour and width.
     *
     * @param Color $color
     * @param float $width
     * @return $this
     */
    public function stroke(Color $color, $width = 1.0): self
    {
        $this->adapter->setColor('stroke', ...$color->toArray());
        $this->adapter->setLineWidth($width);

        return $this;
    }

    /**
     * Set the fill colour.
     *
     * @param Color $color
     * @return $this
     */
    public function fill(Color $color): self
    {
        $this->adapter->setColor('fill', ...$color->toArray());

        return $this;
    }

    /**
     * Set the fill and stroke colours.
     *
     * @param Color $color
     * @return $this
     */
    public function fillAndStroke(Color $color): self
    {
        $this->adapter->setColor('fillstroke', ...$color->toArray());

        return $this;
    }

    /**
     * Draw a circle.
     *
     * @param float $x
     * @param float $y
     * @param float $radius
     * @return $this
     */
    public function circle(float $x, float $y, float $radius): self
    {
        $this->adapter->circle($x, $y, $radius);

        return $this;
    }

    /**
     * Draw a rectangle.
     *
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @return $this
     */
    public function rectangle(float $x, float $y, float $width, float $height): self
    {
        $this->adapter->rect($x, $y, $width, $height);

        return $this;
    }

    /**
     * Set the current point for graphics output.
     *
     * @param float $x
     * @param float $y
     * @return $this
     */
    public function moveTo(float $x, float $y): self
    {
        $this->adapter->moveTo($x, $y);

        return $this;
    }

    /**
     * Draw a line from the current point to another point.
     *
     * @param float $x
     * @param float $y
     * @return $this
     */
    public function lineTo(float $x, float $y): self
    {
        $this->adapter->lineTo($x, $y);

        return $this;
    }

    /**
     * Draw a Bézier curve from the current point, using three more control points.
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param float $x3
     * @param float $y3
     * @return $this
     */
    public function curveTo(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3): self
    {
        $this->adapter->curveto($x1, $y1, $x2, $y2, $x3, $y3);

        return $this;
    }

    /**
     * Paint the fill and stroke.
     *
     * @return $this
     */
    public function paint(): self
    {
        $this->adapter->fillStroke();

        return $this;
    }

    /**
     * Paint the stroke.
     *
     * @return $this
     */
    public function paintStroke(): self
    {
        $this->adapter->stroke();

        return $this;
    }

    /**
     * Paint the fill.
     *
     * @return $this
     */
    public function paintFill(): self
    {
        $this->adapter->fill();

        return $this;
    }
}
