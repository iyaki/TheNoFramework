<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class AnotherSampleMiddleware implements MiddlewareInterface
{
    public function __construct(
        private string $textToAdd = 'AnotherMiddleware'
    ) { }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $response->getBody()->write($this->textToAdd);

        return $response;
    }
}
