<?php

declare(strict_types=1);

namespace TheNoFramework;

use Laminas\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractRequestHandler implements RequestHandlerInterface
{
    protected $httpFactory;

    public function __construct()
    {
        $this->httpFactory = new ResponseFactory();
    }

    abstract public function handle(ServerRequestInterface $request): ResponseInterface;

}
