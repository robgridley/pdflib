<?php

namespace Pdf;

use PDFlib;
use PDFlibException;

class PdfLibAdapter
{
    /**
     * The PDFlib instance.
     *
     * @var PDFlib
     */
    protected $lib;

    /**
     * Create a new adapter instance.
     *
     * @param PDFlib|null $lib
     */
    public function __construct(PDFlib $lib = null)
    {
        $this->lib = $lib ?: new PDFlib;

        $this->setOption('errorPolicy', 'exception');
        $this->setOption('stringFormat', 'utf8');
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
     * @param $key
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
     * @return mixed
     */
    public function getScope()
    {
        return $this->getString($this->getOption('scope'));
    }

    /**
     * Determine if a given scope matches the current scope.
     *
     * @param string $scope
     * @return bool
     */
    public function isScope($scope)
    {
        return $scope == $this->getScope();
    }

    /**
     * Wrapper for PDFlib::scale.
     *
     * @param float $scaleX
     * @param float $scaleY
     */
    public function scale($scaleX, $scaleY)
    {
        $this->lib->scale($scaleX, $scaleY);
    }

    /**
     * Wrapper for PDFlib::begin_document.
     *
     * @param null $filename
     * @param array $options
     */
    public function beginDocument($filename = null, $options = [])
    {
        $this->lib->begin_document($filename, $this->createOptionList($options));
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
        $this->lib->fill_pdfblock(
            $this->getHandleFrom($page),
            $name,
            $this->getHandleFrom($contents),
            $this->createOptionList($options)
        );
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
        $this->lib->fill_imageblock(
            $this->getHandleFrom($page),
            $name,
            $this->getHandleFrom($contents),
            $this->createOptionList($options)
        );
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
        $this->lib->fill_graphicsblock(
            $this->getHandleFrom($page),
            $name,
            $this->getHandleFrom($contents),
            $this->createOptionList($options)
        );
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
     * @param $image
     * @param $key
     * @param array $options
     * @return mixed
     */
    public function infoImage($image, $key, $options = [])
    {
        return $this->lib->info_image(
            $this->getHandleFrom($image),
            $key,
            $this->createOptionList($options)
        );
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
    public function loadFont($name, $encoding = 'unicode', array $options = [])
    {
        return $this->lib->load_font($name, $encoding, $this->createOptionList($options));
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
                $key = $value;
                $value = true;
            }

            $value = $key . '=' . $this->formatOptionListValue($value);
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

        if (is_array($value)) {
            return '{' . implode(' ', $value) . '}';
        }

        return $value;
    }

    /**
     * Get the PDFlib handle.
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
