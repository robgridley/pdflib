<?php

namespace Pdf;

use Countable;
use ArrayAccess;
use LogicException;
use Pdf\Pdi\PdiPage;
use IteratorAggregate;
use Pdf\Color\SpotColor;
use Pdf\Pdi\PdiDocument;
use BadMethodCallException;

/**
 * @mixin PdfLibAdapter
 */
class PdfBuilder implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The current page.
     *
     * @var int
     */
    protected $page = 0;

    /**
     * The total number of pages.
     *
     * @var int
     */
    protected $pages = 0;

    /**
     * The suspended pages.
     *
     * @var int[]
     */
    protected $suspended = [];

    /**
     * Create a new instance.
     *
     * @param PdfLibAdapter|null $adapter
     * @param array $options
     */
    public function __construct(PdfLibAdapter $adapter = null, array $options = [])
    {
        $this->adapter = $adapter ?: new PdfLibAdapter;

        $this->adapter->beginDocument(null, $options);
    }

    /**
     * Handle calls to inaccessible methods by passing them to the PDFlib adapter.
     *
     * @param string $method
     * @param array $arguments
     */
    public function __call($method, array $arguments)
    {
        if (method_exists($this->adapter, $method)) {
            return $this->adapter->$method(...$arguments);
        }

        throw new BadMethodCallException("Method [$method] does not exist");
    }

    /**
     * Add a page to the current document.
     *
     * @param int $width
     * @param int $height
     * @param array $options
     */
    public function addPage($width = 0, $height = 0, array $options = [])
    {
        $this->suspendPage();

        $this->adapter->beginPage($width, $height, $options);
        $this->page = ++$this->pages;
    }

    /**
     * Resume the specified page.
     *
     * @param int $pageNumber
     * @param array $options
     */
    public function resumePage($pageNumber, $options = [])
    {
        $this->suspendPage();

        $this->adapter->resumePage(array_merge(compact('pageNumber'), $options));
        $this->suspended = array_diff($this->suspended, [$pageNumber]);
        $this->page = $pageNumber;
    }

    /**
     * Resume the last suspended page.
     */
    public function resumeLast()
    {
        $this->resumePage(max($this->suspended));
    }

    /**
     * Load an image.
     *
     * @param string $contents
     * @param string $type
     * @param array $options
     * @return Image
     */
    public function loadImage($contents, $type = null, array $options = [])
    {
        return new Image($this->adapter, $contents, $type, $options);
    }

    /**
     * Load a SVG file.
     *
     * @param string $contents
     * @param string $type
     * @param array $options
     * @return Graphics
     */
    public function loadGraphics($contents, $type = 'auto', array $options = [])
    {
        return new Graphics($this->adapter, $contents, $type, $options);
    }

    /**
     * Load a font file.
     *
     * @param string $name
     * @param string $encoding
     * @param array $options
     * @return Font
     */
    public function loadFont($name, $encoding, $options = [])
    {
        return new Font($this->adapter, $name, $encoding, $options);
    }

    /**
     * Load a spot colour from the built-in libraries.
     *
     * @param string $name
     * @param float $tint
     * @return SpotColor
     */
    public function loadSpotColor($name, $tint = 1.0)
    {
        return new SpotColor($this->adapter, $name, $tint);
    }

    /**
     * Import a document.
     *
     * @param string $contents
     * @param array $options
     * @return PdiDocument
     */
    public function import($contents, array $options = [])
    {
        return new PdiDocument($this->adapter, $contents, $options);
    }

    /**
     * Place an imported page onto the current page.
     *
     * @param PdiPage $page
     * @param int $x
     * @param int $y
     * @param array $options
     */
    public function placePage(PdiPage $page, $x = 0, $y = 0, array $options = [])
    {
        $this->adapter->fitPdiPage($page, $x, $y, $options);
    }

    /**
     * Place a graphic on the current page.
     *
     * @param Graphics $graphic
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @param array $options
     */
    public function placeGraphics(Graphics $graphic, $x, $y, $width, $height, array $options = [])
    {
        $options = array_merge(['boxSize' => [$width, $height], 'fitMethod' => 'auto'], $options);

        $this->adapter->fitGraphics($graphic, $x, $y, $options);
    }

    /**
     * Place an image on the current page.
     *
     * @param Image $image
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @param array $options
     */
    public function placeImage(Image $image, $x, $y, $width, $height, array $options = [])
    {
        $options = array_merge(['boxSize' => [$width, $height], 'fitMethod' => 'auto'], $options);

        $this->adapter->fitImage($image, $x, $y, $options);
    }

    /**
     * Create a new table instance.
     *
     * @return Table
     */
    public function newTable()
    {
        return new Table($this->adapter);
    }

    /**
     * Place a table on the current page.
     *
     * @param Table $table
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @param array $options
     * @return string
     */
    public function placeTable(Table $table, $x, $y, $width, $height, array $options = [])
    {
        $llx = $x;
        $lly = $y + $height;
        $urx = $x + $width;
        $ury = $y;

        $result = $this->adapter->fitTable($table, $llx, $lly, $urx, $ury, $options);

        return $result == '_stop' ? false : true;
    }

    /**
     * Create a new textflow instance.
     *
     * @param Font $font
     * @param float $size
     * @param string $text
     * @param array $options
     * @return Textflow
     */
    public function newTextflow(Font $font, $size, $text = null, array $options = [])
    {
        return new Textflow($this->adapter, $font, $size, $text, $options);
    }

    /**
     * Place a textflow on the current page.
     *
     * @param Textflow $textflow
     * @param float $x
     * @param float $y
     * @param float $width
     * @param float $height
     * @param array $options
     * @return string
     */
    public function placeTextflow(Textflow $textflow, $x, $y, $width, $height, array $options = [])
    {
        $llx = $x;
        $lly = $y + $height;
        $urx = $x + $width;
        $ury = $y;

        $result = $this->adapter->fitTextflow($textflow, $llx, $lly, $urx, $ury, $options);

        return $result == '_stop' ? false : true;
    }

    /**
     * Draw vector graphics.
     *
     * @param callable $callback
     */
    public function draw(callable $callback): void
    {
        $this->adapter->save();

        $callback(new Drawing($this->adapter));

        $this->adapter->restore();
    }

    /**
     * Create a new layer instance.
     *
     * @param string $name
     * @param array $options
     * @return Layer
     */
    public function newLayer(string $name, array $options = []): Layer
    {
        return new Layer($this->adapter, $name, $options);
    }

    /**
     * Render the document.
     *
     * @return string
     */
    public function render()
    {
        $this->suspendPage();
        $this->endSuspended();

        $this->adapter->endDocument();

        return $this->adapter->getBuffer();
    }

    /**
     * Get the Internet media type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return 'application/pdf';
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
     * Determine if a global option exists.
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key)
    {
        $this->adapter->optionExists($key);
    }

    /**
     * Get a global option.
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->adapter->getOption($key);
    }

    /**
     * Set a global option.
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        $this->adapter->setOption($key, $value);
    }

    /**
     * Required by the ArrayAccess interface.
     *
     * @param string $key
     * @throws LogicException when called.
     */
    public function offsetUnset($key)
    {
        throw new LogicException('PDF options cannot be unset');
    }

    /**
     * Convert the instance to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Resume and end the suspended pages.
     */
    protected function endSuspended()
    {
        foreach ($this->suspended as $pageNumber) {
            $this->resumePage($pageNumber);
            $this->adapter->endPage();
        }

        $this->suspended = [];
    }

    /**
     * Suspend the current page.
     *
     * @param array $options
     */
    protected function suspendPage($options = [])
    {
        if ($this->adapter->isScope('page')) {
            $this->adapter->suspendPage($options);
            $this->suspended[] = $this->page;
            sort($this->suspended);
            $this->page = 0;
        }
    }

    /**
     * Count the number of pages.
     *
     * @return int
     */
    public function count()
    {
        return $this->pages;
    }

    /**
     * Retrieve the suspended pages iterator.
     *
     * @return SuspendedPageIterator
     */
    public function getIterator()
    {
        $this->suspendPage();

        return new SuspendedPageIterator($this->suspended, $this);
    }
}
