<?php

declare(strict_types=1);

namespace TheNoFramework;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApplicationRunner
{
    public function __construct()
    {
        require __DIR__.'/../vendor/autoload.php';
    }

    public function run(AbstractRequestHandler $requestHandler): void
    {
        $serverRequest = ServerRequestFactory::fromGlobals();

        $handler = $requestHandler;
        foreach ($requestHandler->getMiddlewares() as $middleware) {
            $handler = new class ($middleware, $handler) implements RequestHandlerInterface
            {
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
        $this->emit($handler->handle($serverRequest));
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
