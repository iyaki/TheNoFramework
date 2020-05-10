<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ServiceContainerMock implements ContainerInterface
{
    private array $container = [];

    public function __construct(array $container)
    {
        $this->container = $container;
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new class($id) extends \Exception implements NotFoundExceptionInterface {
                public function __construct($id)
                {
                    parent::__construct("No entry was found for {$id} identifier");
                }
            };
        }
        return $this->container[$id];
    }

    public function has($id)
    {
        return array_key_exists($id, $this->container);
    }
}
