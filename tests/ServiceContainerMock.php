<?php

declare(strict_types=1);

namespace TheNoFramework;

use RuntimeException;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final readonly class ServiceContainerMock implements ContainerInterface
{
    /**
     * @param mixed[] $entries
     */
    public function __construct(
        private array $entries = []
    ) {
    }

    public function get(string $id)
    {
        if (! $this->has($id)) {
            throw new class("No entry was found for {$id} identifier") extends RuntimeException implements NotFoundExceptionInterface { };
        }
        return $this->entries[$id];
    }

    public function has(string $id): bool
    {
        return \array_key_exists($id, $this->entries);
    }
}
