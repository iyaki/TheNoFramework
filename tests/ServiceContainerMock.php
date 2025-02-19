<?php

declare(strict_types=1);

namespace TheNoFramework;

use RuntimeException;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final readonly class ServiceContainerMock implements ContainerInterface
{
    public function __construct(
        private array $entries = []
    ) {
    }

    public function get(string $id)
    {
        if (! $this->has($id)) {
            throw new class($id) extends RuntimeException implements NotFoundExceptionInterface {
                public function __construct(
                    $id
                ) {
                    parent::__construct("No entry was found for {$id} identifier");
                }
            };
        }
        return $this->entries[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }
}
