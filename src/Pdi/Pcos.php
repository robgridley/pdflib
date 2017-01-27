<?php

namespace Pdf\Pdi;

use Pdf\PdfLibAdapter;

class Pcos
{
    /**
     * The PDFlib Adapter instance.
     *
     * @var PdfLibAdapter
     */
    protected $adapter;

    /**
     * The document to query.
     *
     * @var PdiDocument
     */
    protected $document;

    /**
     * Create an instance.
     *
     * @param PdfLibAdapter $adapter
     * @param PdiDocument $document
     */
    public function __construct(PdfLibAdapter $adapter, PdiDocument $document)
    {
        $this->adapter = $adapter;
        $this->document = $document;
    }

    /**
     * Get the value(s) for the given path.
     *
     * @param mixed $path
     * @return mixed
     */
    public function getValue($path)
    {
        $type = $this->getType($path);

        switch ($type) {
            case 'boolean':
                return (bool)$this->getNumberValue($path);
            case 'number':
                return $this->getNumberValue($path);
            case 'name':
            case 'string':
                return $this->getStringValue($path);
            case 'array':
                return $this->getArrayValue($path);
            case 'dict':
                return $this->getDictionaryValue($path);
            case 'stream':
            case 'fstream':
                return $this->getStreamValue($path);
        }
    }

    /**
     * Get the value type for the given path.
     *
     * @param mixed $path
     * @return string
     */
    public function getType($path)
    {
        return $this->adapter->pcosGetString($this->document, "type:$path");
    }

    /**
     * Get the length of the value for the given path.
     *
     * array    Number of elements in the array.
     * dict     Number of key/value pairs in the dictionary.
     * stream   Number of key/value pairs in the stream dictionary.
     * fstream  Same as stream.
     * other    Always returns 0.
     *
     * @param mixed $path
     * @return int
     */
    public function getLength($path)
    {
        return (int)$this->adapter->pcosGetNumber($this->document, "length:$path");
    }

    /**
     * Get an array value.
     *
     * @param mixed $path
     * @return array
     */
    protected function getArrayValue($path)
    {
        $array = [];
        $count = $this->getLength($path);

        for ($i = 0; $i < $count; $i++) {
            $array[] = $this->getValue("{$path}[$i]");
        }

        return $array;
    }

    /**
     * Get a dictionary (associative array) value.
     *
     * @param mixed $path
     * @return PcosDictionary
     */
    protected function getDictionaryValue($path)
    {
        return new PcosDictionary($this, $path);
    }

    /**
     * Get a string value.
     *
     * @param mixed $path
     * @return string
     */
    public function getStringValue($path)
    {
        return (string)$this->adapter->pcosGetString($this->document, $path);
    }

    /**
     * Get a numeric value.
     *
     * @param mixed $path
     * @return float
     */
    public function getNumberValue($path)
    {
        return $this->adapter->pcosGetNumber($this->document, $path);
    }

    /**
     * Get a stream value.
     *
     * @param mixed $path
     * @return string
     */
    public function getStreamValue($path)
    {
        return $this->adapter->pcosGetStream($this->document, $path);
    }
}
