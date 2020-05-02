<?php

declare(strict_types=1);

namespace TheNoFramework;

use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractRequestHandler implements RequestHandlerInterface
{
    protected ResponseFactoryInterface $responseFactory;
    
    private array $middlewares = [];

    public function __construct()
    {
        $this->responseFactory = new ResponseFactory();
    }

    abstract public function handle(ServerRequestInterface $request): ResponseInterface;

    /**
     * Add PSR-15 middlewares to be executed with the handler
     *
     * @param Psr\Http\Server\MiddlewareInterface[] $middlewares
     * @return void
     */
    protected function addMiddlewares(array $middlewares): void
    {
        $this->middlewares = (function (MiddlewareInterface ...$middlewares) {
            return $middlewares;
        })(...[...$this->middlewares, ...$middlewares]);
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

}
