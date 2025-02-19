<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Http\Message\StreamInterface;

final class StreamMock implements StreamInterface
{
    public function __construct(
        private string $stringContent = ''
    ) { }

    public function __toString(): string
    {
        return $this->stringContent;
    }

    public function close(): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function detach()
    {
    }

    public function getSize(): ?int
    {
        throw new \RuntimeException('Not implemented');
        // return \strlen($this->fakedStream);
    }

    public function tell(): int
    {
        throw new \RuntimeException('Not implemented');
    }

    public function eof(): bool
    {
        throw new \RuntimeException('Not implemented');
    }

    public function isSeekable(): bool
    {
        throw new \RuntimeException('Not implemented');
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function rewind(): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function isWritable(): bool
    {
        return true;
    }

    public function write(string $string): int
    {
        $this->stringContent .= $string;
        return strlen($string);
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function read(int $length): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getContents(): string
    {
        throw new \RuntimeException('Not implemented');
    }

    public function getMetadata(?string $key = null)
    {
        throw new \RuntimeException('Not implemented');
    }
}
