<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SampleMiddleware implements MiddlewareInterface
{
    private string $textToAdd;

    public function __construct(string $textToAdd)
    {
        $this->textToAdd = $textToAdd;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $response->getBody()->write($this->textToAdd);

        return $response;
    }
}
