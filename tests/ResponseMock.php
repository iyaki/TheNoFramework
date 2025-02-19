<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final readonly class ResponseMock implements ResponseInterface
{
    private StreamInterface $stream;

    public function __construct(
        string $body = '',
        private int $status = 200,
        private string $reasonPhrase = ''
    ) {
        $this->stream = new StreamMock($body);
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        $response = clone $this;
        $response->status = $code;
        $response->reasonPhrase = $reasonPhrase;
        return $response;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function getProtocolVersion(): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function withProtocolVersion(string $version): static
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getHeaders(): array
    {
        throw new \RuntimeException('Not implemented');
    }

    public function hasHeader(string $name): bool
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getHeader(string $name): array
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getHeaderLine(string $name): string
    {
    }

    public function withHeader(string $name, $value): static
    {
        return clone $this;
    }

    public function withAddedHeader(string $name, $value): static
    {
    }

    public function withoutHeader(string $name): static
    {
    }

    public function getBody(): StreamInterface
    {
        return $this->stream;
    }

    public function withBody(StreamInterface $body): static
    {
    }
}
