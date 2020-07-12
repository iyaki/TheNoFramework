<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApplicationRunner
{
    private ?ContainerInterface $serviceContainer;

    public function __construct(?ContainerInterface $serviceContainer = null)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * Runs the given request handler and middlewares
     *
     * @return ResponseInterface
     * @param string $requestHandler
     * @param ServerRequestInterface $serverRequest
     * @param string[] $middlewares
     */
    public function run(
        string $requestHandlerClass,
        ServerRequestInterface $serverRequest,
        array $middlewares = []
    ): ResponseInterface {
        $handler = $this->makeChainedHandler(
            $this->getHandlerFrom($requestHandlerClass),
            $this->getMiddlewaresFrom($middlewares)
        );

        return $handler->handle($serverRequest);
    }

    public function __clone()
    {
        throw new \DomainException('Cloning this class is not allowed');
    }

    public function __sleep()
    {
        throw new \DomainException('This class can\'t be serialized');
    }

    /**
     * Get the request handler object
     *
     * @param string $requestHandlerClass
     * @return RequestHandlerInterface
     */
    private function getHandlerFrom(string $requestHandlerClass): RequestHandlerInterface
    {
        return (
            null !== $this->serviceContainer && $this->serviceContainer->has($requestHandlerClass)
            ? $this->serviceContainer->get($requestHandlerClass)
            : new $requestHandlerClass()
        );
    }

    /**
     * Get the middlewares objects array
     *
     * @param string[] $middlewareClassArray
     * @return MiddlewareInterface[]
     */
    private function getMiddlewaresFrom(array $middlewareClassArray): array
    {
        return array_map(
            fn (string $middlewareClass): MiddlewareInterface => (
                null !== $this->serviceContainer && $this->serviceContainer->has($middlewareClass)
                ? $this->serviceContainer->get($middlewareClass)
                : new $middlewareClass()
            ),
            $middlewareClassArray
        );
    }

    /**
     * Creates a request handler chaining the proper hndler and the middlewares
     *
     * @param RequestHandlerInterface $handler
     * @param MiddlewareInterface[] $middlewares
     * @return RequestHandlerInterface
     */
    private function makeChainedHandler(RequestHandlerInterface $handler, array $middlewares): RequestHandlerInterface
    {
        $middlewares = (function (MiddlewareInterface ...$middlewares) {
            return $middlewares;
        })(...$middlewares);

        foreach ($middlewares as $middleware) {
            $handler = new class($middleware, $handler) implements RequestHandlerInterface {
                private MiddlewareInterface $middleware;
                private RequestHandlerInterface $handler;

                public function __construct(MiddlewareInterface $middleware, RequestHandlerInterface $handler)
                {
                    $this->middleware = $middleware;
                    $this->handler = $handler;
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
