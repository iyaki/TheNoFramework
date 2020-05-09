<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Http\Message\StreamInterface;

class StreamMock implements StreamInterface
{
    private $fakedStream = '';

    public function __construct(string $content = '')
    {
        $this->fakedStream = $content;
    }

    public function write($string)
    {
        $this->fakedStream .= $string;
        return strlen($string);
    }

    public function __toString()
    {
        return $this->fakedStream;
    }

    public function close()
    {
    }

    public function detach()
    {
    }

    public function getSize()
    {
    }

    public function tell()
    {
    }

    public function eof()
    {
    }

    public function isSeekable()
    {
    }

    public function seek($offset, $whence = SEEK_SET)
    {
    }

    public function rewind()
    {
    }

    public function isWritable()
    {
    }

    public function isReadable()
    {
    }

    public function read($length)
    {
    }

    public function getContents()
    {
    }

    public function getMetadata($key = null)
    {
    }
}
