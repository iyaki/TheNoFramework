<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class SampleRequestHandlerWithDependencies implements RequestHandlerInterface
{
    public function __construct(
        private ResponseInterface $response
    ) { }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->response->getBody()->write((string) $request->getBody());

        return $this->response;
    }
}
