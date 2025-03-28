<?php

namespace Pdf;

use PDFlib;
use PDFlibException;

class PdfLibAdapter
{
    const SCOPE_OBJECT = 'object';
    const SCOPE_DOCUMENT = 'document';
    const SCOPE_GLYPH = 'glyph';
    const SCOPE_FONT = 'font';
    const SCOPE_PATTERN = 'pattern';
    const SCOPE_TEMPLATE = 'template';
    const SCOPE_PAGE = 'page';
    const SCOPE_PATH = 'path';

    /**
     * The PDFlib instance.
     *
     * @var PDFlib
     */
    protected $lib;

    /**
     * The default options.
     *
     * @var array
     */
    protected static $defaults = [
        'errorPolicy' => 'exception',
        'stringFormat' => 'utf8',
    ];

    /**
     * Create a new adapter instance.
     *
     * @param PDFlib|null $lib
     */
    public function __construct(PDFlib $lib = null)
    {
        $this->lib = $lib ?: new PDFlib;

        $this->applyDefaults();
    }

    /**
     * Set the specified default option.
     *
     * @param string $key
     * @param mixed $value
     */
    public static function setDefault($key, $value)
    {
        static::$defaults[$key] = $value;
    }

    /**
     * Apply default options.
     */
    protected function applyDefaults()
    {
        foreach (static::$defaults as $key => $value) {
            $this->setOption($key, $value);
        }
    }

    /**
     * Wrapper for PDFlib::set_option.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setOption($key, $value = true)
    {
        $this->lib->set_option($key . '=' . $this->formatOptionListValue($value));
    }

    /**
     * Wrapper for PDFlib::get_option.
     *
     * @param string $key
     * @param array $options
     * @return mixed
     */
    public function getOption($key, $options = [])
    {
        return $this->lib->get_option($key, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::get_string.
     *
     * @param int $index
     * @param array $options
     * @return string
     */
    public function getString($index, $options = [])
    {
        return $this->lib->get_string($index, $this->createOptionList($options));
    }

    /**
     * Determine if an option exists.
     *
     * @param string $key
     * @return bool
     * @throws PDFlibException
     */
    public function optionExists($key)
    {
        try {
            $this->getOption($key);
        } catch (PDFlibException $e) {
            if ($e->getCode() === 1202) {
                return false;
            }

            throw $e;
        }

        return true;
    }

    /**
     * Gets the current scope.
     *
     * @return string
     */
    public function getScope()
    {
        return $this->getString($this->getOption('scope'));
    }

    /**
     * Determine if the specified scope matches the current scope.
     *
     * @param string $scope
     * @return bool
     */
    public function isScope($scope)
    {
        return $scope == $this->getScope();
    }

    /**
     * Wrapper for PDFlib::begin_document.
     *
     * @param string|null $filename
     * @param array $options
     */
    public function beginDocument(string $filename = null, array $options = [])
    {
        $this->lib->begin_document((string)$filename, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::end_document.
     *
     * @param array $options
     */
    public function endDocument($options = [])
    {
        $this->lib->end_document($this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::begin_page_ext.
     *
     * @param int $width
     * @param int $height
     * @param array $options
     */
    public function beginPage($width = 0, $height = 0, $options = [])
    {
        $this->lib->begin_page_ext($width, $height, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::suspend_page.
     *
     * @param array $options
     */
    public function endPage($options = [])
    {
        $this->lib->end_page_ext($this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::resume_page.
     *
     * @param array $options
     */
    public function suspendPage($options = [])
    {
        $this->lib->suspend_page($this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::end_page_ext.
     *
     * @param array $options
     */
    public function resumePage($options = [])
    {
        $this->lib->resume_page($this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::open_pdi_document.
     *
     * @param $filename
     * @param array $options
     * @return int
     */
    public function openPdiDocument($filename, $options = [])
    {
        return $this->lib->open_pdi_document($filename, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::close_pdi_document.
     *
     * @param Handleable|int $document
     */
    public function closePdiDocument($document)
    {
        $this->lib->close_pdi_document($this->getHandleFrom($document));
    }

    /**
     * Wrapper for PDFlib::open_pdi_page.
     *
     * @param Handleable|int $document
     * @param int $page
     * @param array $options
     * @return int
     */
    public function openPdiPage($document, $page, $options = [])
    {
        return $this->lib->open_pdi_page($this->getHandleFrom($document), $page, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::close_pdi_page.
     *
     * @param Handleable|int $page
     */
    public function closePdiPage($page)
    {
        $this->lib->close_pdi_page($this->getHandleFrom($page));
    }

    /**
     * Wrapper for PDFlib::fit_pdi_page.
     *
     * @param Handleable|int $page
     * @param double $x
     * @param double $y
     * @param array $options
     */
    public function fitPdiPage($page, $x = 0.0, $y = 0.0, $options = [])
    {
        $this->lib->fit_pdi_page($this->getHandleFrom($page), $x, $y, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::info_pdi_page.
     *
     * @param Handleable|int $page
     * @param string $key
     * @param array $options
     * @return mixed
     */
    public function infoPdiPage($page, $key, $options = [])
    {
        return $this->lib->info_pdi_page($this->getHandleFrom($page), $key, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::pcos_get_number.
     *
     * @param Handleable|int $document
     * @param string $path
     * @return float
     */
    public function pcosGetNumber($document, $path)
    {
        return $this->lib->pcos_get_number($this->getHandleFrom($document), $path);
    }

    /**
     * Wrapper for PDFlib::pcos_get_string.
     *
     * @param Handleable|int $document
     * @param string $path
     * @return string
     */
    public function pcosGetString($document, $path)
    {
        return $this->lib->pcos_get_string($this->getHandleFrom($document), $path);
    }

    /**
     * Wrapper for PDFlib::pcos_get_stream.
     *
     * @param Handleable|int $document
     * @param string $path
     * @param array $options
     * @return string
     */
    public function pcosGetStream($document, $path, $options = [])
    {
        return $this->lib->pcos_get_stream($this->getHandleFrom($document), $this->createOptionList($options), $path);
    }

    /**
     * Wrapper for PDFlib::create_pvf.
     *
     * @param string $path
     * @param string $contents
     * @param array $options
     */
    public function createPvf($path, $contents, $options = [])
    {
        $this->lib->create_pvf($path, $contents, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::deletePvf.
     *
     * @param string $path
     */
    public function deletePvf($path)
    {
        $this->lib->delete_pvf($path);
    }

    /**
     * Wrapper for PDFlib::info_pvf.
     *
     * @param string $path
     * @param string $key
     * @return mixed
     */
    public function infoPvf($path, $key)
    {
        return $this->lib->info_pvf($path, $key);
    }

    /**
     * Wrapper for PDFlib::fill_textblock.
     *
     * @param Handleable|int $page
     * @param string $name
     * @param string $contents
     * @param array $options
     */
    public function fillTextBlock($page, $name, $contents, $options = [])
    {
        $this->lib->fill_textblock($this->getHandleFrom($page), $name, $contents, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::fill_pdfblock.
     *
     * @param Handleable|int $page
     * @param $name
     * @param Handleable|int $contents
     * @param array $options
     */
    public function fillPdfBlock($page, $name, $contents, $options = [])
    {
        $page = $this->getHandleFrom($page);
        $contents = $this->getHandleFrom($contents);

        $this->lib->fill_pdfblock($page, $name, $contents, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::fill_imageblock.
     *
     * @param Handleable|int $page
     * @param $name
     * @param Handleable|int $contents
     * @param array $options
     */
    public function fillImageBlock($page, $name, $contents, $options = [])
    {
        $page = $this->getHandleFrom($page);
        $contents = $this->getHandleFrom($contents);

        $this->lib->fill_imageblock($page, $name, $contents, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::fill_graphicsblock.
     *
     * @param Handleable|int $page
     * @param $name
     * @param Handleable|int $contents
     * @param array $options
     */
    public function fillGraphicsBlock($page, $name, $contents, $options = [])
    {
        $page = $this->getHandleFrom($page);
        $contents = $this->getHandleFrom($contents);

        $this->lib->fill_graphicsblock($page, $name, $contents, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::load_image.
     *
     * @param string $path
     * @param string $type
     * @param array $options
     * @return int
     */
    public function loadImage($path, $type = null, $options = [])
    {
        return $this->lib->load_image($type ?: 'auto', $path, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::close_image.
     *
     * @param Handleable|int $image
     */
    public function closeImage($image)
    {
        $this->lib->close_image($this->getHandleFrom($image));
    }

    /**
     * Wrapper for PDFlib::info_image.
     *
     * @param Handleable|int $image
     * @param string $key
     * @param array $options
     * @return mixed
     */
    public function infoImage($image, $key, $options = [])
    {
        return $this->lib->info_image($this->getHandleFrom($image), $key, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::fit_image.
     *
     * @param Handleable|int $image
     * @param float $x
     * @param float $y
     * @param array $options
     */
    public function fitImage($image, $x = 0.0, $y = 0.0, $options = [])
    {
        $this->lib->fit_image($this->getHandleFrom($image), $x, $y, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::load_graphics.
     *
     * @param string $path
     * @param string $type
     * @param array $options
     * @return int
     */
    public function loadGraphics($path, $type = null, $options = [])
    {
        return $this->lib->load_graphics($type ?: 'auto', $path, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::close_graphics.
     *
     * @param int|Handleable $graphic
     */
    public function closeGraphics($graphic)
    {
        $this->lib->close_graphics($this->getHandleFrom($graphic));
    }

    /**
     * Wrapper for PDFlib::fit_graphics.
     *
     * @param int|Handleable $graphic
     * @param double $x
     * @param double $y
     * @param array $options
     */
    public function fitGraphics($graphic, $x = 0.0, $y = 0.0, $options = [])
    {
        $this->lib->fit_graphics($this->getHandleFrom($graphic), $x, $y, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::info_graphics.
     *
     * @param int|Handleable $graphic
     * @param string $key
     * @param array $options
     * @return mixed
     */
    public function infoGraphics($graphic, $key, $options = [])
    {
        return $this->lib->info_graphics($this->getHandleFrom($graphic), $key, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::load_font.
     *
     * @param string $name
     * @param string $encoding
     * @param array $options
     * @return int
     */
    public function loadFont($name, $encoding, array $options = [])
    {
        return $this->lib->load_font($name, $encoding, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::info_font.
     *
     * @param int|Handleable $font
     * @param string $key
     * @param array $options
     * @return mixed
     */
    public function infoFont($font, $key, array $options = [])
    {
        return $this->lib->info_font($this->getHandleFrom($font), $key, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::close_font.
     *
     * @param int|Handleable $font
     */
    public function closeFont($font)
    {
        $this->lib->close_font($this->getHandleFrom($font));
    }

    /**
     * Wrapper for PDFlib::set_text_option.
     *
     * @param array $options
     */
    public function setTextOption(array $options)
    {
        $this->lib->set_text_option($this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::add_textflow.
     *
     * @param int $textflow
     * @param string|null $text
     * @param array $options
     * @return int
     */
    public function addTextflow($textflow = 0, $text = null, array $options = [])
    {
        return $this->lib->add_textflow($this->getHandleFrom($textflow), (string)$text, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::create_textflow.
     *
     * @param string|null $text
     * @param array $options
     * @return int
     */
    public function createTextflow($text = null, array $options = [])
    {
        return $this->lib->create_textflow((string)$text, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::fit_textflow.
     *
     * @param int|Handleable $textflow
     * @param float $llx
     * @param float $lly
     * @param float $urx
     * @param float $ury
     * @param array $options
     * @return string
     */
    public function fitTextflow($textflow, $llx, $lly, $urx, $ury, array $options = [])
    {
        $textflow = $this->getHandleFrom($textflow);

        return $this->lib->fit_textflow($textflow, $llx, $lly, $urx, $ury, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::info_textflow.
     *
     * @param int|Handleable $textflow
     * @param string $key
     * @return mixed
     */
    public function infoTextflow($textflow, $key)
    {
        return $this->lib->info_textflow($this->getHandleFrom($textflow), $key);
    }

    /**
     * Wrapper for PDFlib::delete_textflow.
     *
     * @param int|Handleable $textflow
     * @return void
     */
    public function deleteTextflow($textflow)
    {
        $this->lib->delete_textflow($this->getHandleFrom($textflow));
    }

    /**
     * Wrapper for PDFlib::add_table_cell.
     *
     * @param int|Handleable $table
     * @param int $column
     * @param int $row
     * @param string $text
     * @param array $options
     * @return int
     */
    public function addTableCell($table, $column, $row, $text, array $options = [])
    {
        $table = $this->getHandleFrom($table);

        return $this->lib->add_table_cell($table, $column, $row, (string)$text, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::fit_table.
     *
     * @param int|Handleable $table
     * @param float $llx
     * @param float $lly
     * @param float $urx
     * @param float $ury
     * @param array $options
     * @return string
     */
    public function fitTable($table, $llx, $lly, $urx, $ury, array $options = [])
    {
        $table = $this->getHandleFrom($table);

        return $this->lib->fit_table($table, $llx, $lly, $urx, $ury, $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::info_table.
     *
     * @param int|Handleable $table
     * @param string $key
     * @return mixed
     */
    public function infoTable($table, $key)
    {
        return $this->lib->info_table($this->getHandleFrom($table), $key);
    }

    /**
     * Wrapper for PDFlib::delete_table.
     *
     * @param int|Handleable $table
     * @param array $options
     */
    public function deleteTable($table, array $options = [])
    {
        $this->lib->delete_table($this->getHandleFrom($table), $this->createOptionList($options));
    }

    /**
     * Wrapper for PDFlib::makespotcolor.
     *
     * @param string $name
     * @return int
     */
    public function makeSpotColor($name)
    {
        return $this->lib->makespotcolor($name);
    }

    /**
     * Draw a rectangle.
     *
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     */
    public function rect(float $x, float $y, float $width, float $height): void
    {
        $this->lib->rect($x, $y, $width, $height);
    }

    /**
     * Draw a circle.
     *
     * @param float $x
     * @param float $y
     * @param float $radius
     */
    public function circle(float $x, float $y, float $radius): void
    {
        $this->lib->circle($x, $y, $radius);
    }

    /**
     * Set the current color space and color for the graphics and text state.
     *
     * @param string $type
     * @param string $colorSpace
     * @param float|int $c1
     * @param float|int|null $c2
     * @param float|int|null $c3
     * @param float|int|null $c4
     */
    public function setColor(string $type, string $colorSpace, $c1, $c2 = null, $c3 = null, $c4 = null): void
    {
        $this->lib->setcolor($type, $colorSpace, $c1, $c2, $c3, $c4);
    }

    /**
     * Set one or more graphics appearance options.
     *
     * @param array $options
     * @return void
     */
    public function setGraphicsOption(array $options): void
    {
        $this->lib->set_graphics_option($this->createOptionList($options));
    }

    /**
     * Fill the interior of the path with the current fill color.
     */
    public function fill(): void
    {
        $this->lib->fill();
    }

    /**
     * Stroke the path with the current line width and current stroke color.
     */
    public function stroke(): void
    {
        $this->lib->stroke();
    }

    /**
     * Fill and stroke the path with the current fill and stroke color.
     */
    public function fillStroke(): void
    {
        $this->lib->fill_stroke();
    }

    /**
     * Define a color shading (color gradient) between two or more colors.
     *
     * @param string $type
     * @param float $x0
     * @param float $y0
     * @param float $x1
     * @param float $y1
     * @param float|null $c1
     * @param float|null $c2
     * @param float|null $c3
     * @param float|null $c4
     * @param array $options
     * @return int
     */
    public function shading(string $type, float $x0, float $y0, float $x1, float $y1, float $c1 = null, float $c2 = null, float $c3 = null, float $c4 = null, array $options = []): int
    {
        return $this->lib->shading($type, $x0, $y0, $x1, $y1, (float)$c1, (float)$c2, (float)$c3, (float)$c4, $this->createOptionList($options));
    }

    /**
     * Define a shading pattern using a shading object.
     *
     * @param Handleable|int $shading
     * @param array $options
     * @return int
     */
    public function shadingPattern($shading, array $options = []): int
    {
        return $this->lib->shading_pattern($this->getHandleFrom($shading), $this->createOptionList($options));
    }

    /**
     * Define a shading pattern using a shading object.
     *
     * @param Handleable|int $shading
     * @return void
     */
    public function shadingFill($shading): void
    {
        $this->lib->shfill($this->getHandleFrom($shading));
    }

    /**
     * Set the current line width.
     *
     * @param float $width
     */
    public function setLineWidth(float $width): void
    {
        $this->lib->setlinewidth($width);
    }

    /**
     * Set the current point for graphics output.
     *
     * @param float $x
     * @param float $y
     */
    public function moveTo(float $x, float $y): void
    {
        $this->lib->moveto($x, $y);
    }

    /**
     * Draw a line from the current point to another point.
     *
     * @param float $x
     * @param float $y
     */
    public function lineTo(float $x, float $y): void
    {
        $this->lib->lineto($x, $y);
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
     */
    public function curveTo(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3): void
    {
        $this->lib->curveto($x1, $y1, $x2, $y2, $x3, $y3);
    }

    /**
     * Save the current graphics state.
     */
    public function save(): void
    {
        $this->lib->save();
    }

    /**
     * Restore the previously saved graphics state.
     */
    public function restore(): void
    {
        $this->lib->restore();
    }

    /**
     * Create a graphics state object subject to various options.
     *
     * @param array $options
     * @return int
     */
    public function createGraphicsState(array $options)
    {
        return $this->lib->create_gstate($this->createOptionList($options));
    }

    /**
     * Activate a graphics state object.
     *
     * @param Handleable|int $state
     * @return void
     */
    public function setGraphicsState($state): void
    {
        $this->lib->set_gstate($this->getHandleFrom($state));
    }

    /**
     * Translate the origin of the coordinate system.
     *
     * @param float $x
     * @param float $y
     */
    public function translate(float $x, float $y): void
    {
        $this->lib->translate($x, $y);
    }

    /**
     * Scale the coordinate system.
     *
     * @param float $x
     * @param float $y
     */
    public function scale(float $x, float $y): void
    {
        $this->lib->scale($x, $y);
    }

    /**
     * Rotate the coordinate system.
     *
     * @param float $phi
     */
    public function rotate(float $phi): void
    {
        $this->lib->rotate($phi);
    }

    /**
     * Create a new layer definition.
     *
     * @param string $name
     * @param array $options
     * @return int
     */
    public function defineLayer(string $name, array $options = []): int
    {
        return $this->lib->define_layer($name, $this->createOptionList($options));
    }

    /**
     * Define layer relationships.
     *
     * @param string $type
     * @param array $options
     */
    public function setLayerDependency(string $type, array $options = []): void
    {
        $this->lib->set_layer_dependency($type, $this->createOptionList($options));
    }

    /**
     * Start a layer for subsequent output on the page.
     *
     * @param int|Handleable $layer
     */
    public function beginLayer($layer): void
    {
        $this->lib->begin_layer($this->getHandleFrom($layer));
    }

    /**
     * Deactivate all active layers.
     */
    public function endLayer(): void
    {
        $this->lib->end_layer();
    }

    /**
     * Convert an array to a PDFlib option list.
     *
     * @param array $options
     * @param array $defaults
     * @return string
     */
    protected function createOptionList(array $options, array $defaults = [])
    {
        $options = array_merge($defaults, $options);

        foreach ($options as $key => &$value) {
            if (is_int($key)) {
                $value = $this->formatOptionListValue($value);
            } else {
                $value = $key . '=' . $this->formatOptionListValue($value);
            }
        }

        return implode(' ', $options);
    }

    /**
     * Format values for PDFlib option list.
     *
     * @param $value
     * @return string
     */
    protected function formatOptionListValue($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        if (is_array($value)) {
            return '{' . $this->createOptionList($value) . '}';
        }

        if ($value instanceof Handleable) {
            return $value->getHandle();
        }

        if (preg_match('/\s|=/', $value)) {
            return '{' . $value . '}';
        }

        return $value;
    }

    /**
     * Get the PDFlib handle from the specified source.
     *
     * @param Handleable|int $source
     * @return int
     */
    protected function getHandleFrom($source)
    {
        return ($source instanceof Handleable) ? $source->getHandle() : $source;
    }

    /**
     * Fetch PDF document data from memory.
     *
     * @return string
     */
    public function getBuffer()
    {
        return $this->lib->get_buffer();
    }
}
