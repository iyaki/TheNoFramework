<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApplicationRunner
{
    private ?ContainerInterface $serviceContainer;

    public function __construct()
    {
        require Options::AUTOLOAD_PATH;

        if (Options::SERVICE_CONTAINER_WRAPPER) {
            $this->serviceContainer = require Options::SERVICE_CONTAINER_WRAPPER;
        }
    }

    public function run($requestHandler): void
    {
        $serverRequest = ServerRequestFactory::fromGlobals();

        $handler = $this->getHandlerFrom($requestHandler);
        foreach ($requestHandler->getMiddlewares() as $middleware) {
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
        $this->emit($handler->handle($serverRequest));
    }

    private function emit(ResponseInterface $response): void
    {
        if (!$response->hasHeader('Content-Disposition')
            && !$response->hasHeader('Content-Range')
        ) {
            (new SapiEmitter())->emit($response);
            return;
        }
        (new SapiStreamEmitter())->emit($response);
    }


    private function getHandlerFrom($requestHandler): AbstractRequestHandler
    {
        if (is_string($requestHandler)) {
            if (null === $this->serviceContainer) {
                throw new \Exception('A configured properly configured service container (PSR-11 compliant) is required to use strings as argument of '.ApplicationRunner::class.'::run()');
            }
            return $this->serviceContainer->get($requestHandler);
        }

        if ($requestHandler instanceof AbstractRequestHandler) {
            return $requestHandler;
        }

        throw new \TypeError(ApplicationRunner::class.'::run() only accepts strings (keys for the service container) or instances of '.AbstractRequestHandler::class.' as argument');
    }
}
