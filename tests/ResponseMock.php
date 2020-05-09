<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseMock implements ResponseInterface
{
    private $status;
    private $reasonPhrase;
    private $stream;

    public function __construct(string $body = '', int $status = 200)
    {
        $this->stream = new StreamMock($body);
        $this->status = $status;
    }

    public function getStatusCode()
    {
        return $this->status;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $response = clone $this;
        $response->status = $code;
        $response->reasonPhrase = $reasonPhrase;
        return $response;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    public function getProtocolVersion()
    {
    }

    public function withProtocolVersion($version)
    {
    }
    public function getHeaders()
    {
    }

    public function hasHeader($name)
    {
    }

    public function getHeader($name)
    {
    }

    public function getHeaderLine($name)
    {
    }

    public function withHeader($name, $value)
    {
        return clone $this;
    }

    public function withAddedHeader($name, $value)
    {
    }

    public function withoutHeader($name)
    {
    }

    public function getBody()
    {
        return $this->stream;
    }

    public function withBody(StreamInterface $body)
    {
    }
}
