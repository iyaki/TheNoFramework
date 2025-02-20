<?php

declare(strict_types=1);

namespace TheNoFramework;

use BadMethodCallException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class ApplicationRunner
{
    public function __construct(
        private ?ContainerInterface $serviceContainer = null
    ) {
    }

    public function __clone()
    {
        throw new BadMethodCallException('Cloning this class is not allowed');
    }

    public function __sleep()
    {
        throw new BadMethodCallException('This class can\'t be serialized');
    }

    /**
     * Runs the given request handler and middlewares
     *
     * @psalm-param class-string $requestHandlerClass
     * @psalm-param class-string[] $middlewares
     */
    public function run(
        string $requestHandlerClass,
        ServerRequestInterface $serverRequest,
        array $middlewares = []
    ): ResponseInterface {
        $handler = $this->makeChainedHandler(
            $this->getHandlerFrom($requestHandlerClass),
            ...$this->getMiddlewaresFrom($middlewares)
        );

        return $handler->handle($serverRequest);
    }

    /**
     * Get the request handler object
     *
     * @param class-string $requestHandlerClass
     */
    private function getHandlerFrom(string $requestHandlerClass): RequestHandlerInterface
    {
        /** @phpstan-ignore return.type */
        return $this->serviceContainer instanceof ContainerInterface && $this->serviceContainer->has($requestHandlerClass)
            ? $this->serviceContainer->get($requestHandlerClass)
            : new $requestHandlerClass()
        ;
    }

    /**
     * Get the middlewares objects array
     *
     * @param class-string[] $middlewareClassArray
     * @return MiddlewareInterface[]
     */
    private function getMiddlewaresFrom(array $middlewareClassArray): array
    {
        return \array_map(
            fn (string $middlewareClass): MiddlewareInterface => (
                /** @phpstan-ignore return.type */
                $this->serviceContainer instanceof ContainerInterface && $this->serviceContainer->has($middlewareClass)
                    ? $this->serviceContainer->get($middlewareClass)
                    : new $middlewareClass()
            ),
            $middlewareClassArray
        );
    }

    /**
     * Creates a request handler chaining the proper hndler and the middlewares
     */
    private function makeChainedHandler(RequestHandlerInterface $handler, MiddlewareInterface ...$middlewares): RequestHandlerInterface
    {
        foreach ($middlewares as $middleware) {
            $handler = new readonly class($middleware, $handler) implements RequestHandlerInterface {
                public function __construct(
                    private MiddlewareInterface $middleware,
                    private RequestHandlerInterface $handler
                ) {
                }

                public function handle(ServerRequestInterface $request): ResponseInterface
                {
                    return $this->middleware->process($request, $this->handler);
                }
            };
        }

        return $handler;
    }
}
