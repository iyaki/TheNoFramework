<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SampleRequestHandlerWithDependencies implements RequestHandlerInterface
{
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->response->getBody()->write((string) $request->getBody());

        return $this->response;
    }
}
