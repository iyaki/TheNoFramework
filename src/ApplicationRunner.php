<?php

declare(strict_types=1);

namespace TheNoFramework;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApplicationRunner
{
    public function __construct()
    {
        require __DIR__.'/../vendor/autoload.php';
    }

    public function run(RequestHandlerInterface $requestHandler): void
    {
        $serverRequest = ServerRequestFactory::fromGlobals();
        $this->emit($requestHandler->handle($serverRequest));
    }

    private function emit(ResponseInterface $response): void
    {
        if (! $response->hasHeader('Content-Disposition')
            && ! $response->hasHeader('Content-Range')
        ) {
            (new SapiEmitter())->emit($response);
            return;
        }
        (new SapiStreamEmitter())->emit($response);
    }
}
