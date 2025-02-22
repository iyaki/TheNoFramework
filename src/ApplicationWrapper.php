<?php

declare(strict_types=1);

namespace TheNoFramework;

use BadMethodCallException;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ApplicationWrapper
{
    private const string ENV_SERVICE_CONTAINER_WRAPPER = 'SERVICE_CONTAINER_WRAPPER';

    public function __clone()
    {
        throw new BadMethodCallException('Cloning this class is not allowed');
    }

    public function __sleep()
    {
        throw new BadMethodCallException("This class can't be serialized");
    }

    /**
     * Runs the given request handler and middlewares
     *
     * @param class-string<RequestHandlerInterface> $requestHandlerClass
     * @param class-string<MiddlewareInterface>[] $middlewares
     */
    public static function run(string $requestHandlerClass, array $middlewares = []): void
    {

        $applicationRunner = new ApplicationRunner(self::getServiceContainer());

        $serverRequest = ServerRequestFactory::fromGlobals();

        $response = $applicationRunner->run($requestHandlerClass, $serverRequest, $middlewares);

        self::emit($response);
    }

    private static function getServiceContainer(): ?ContainerInterface
    {
        $serviceContainer = \getenv(self::ENV_SERVICE_CONTAINER_WRAPPER);
        if (\is_string($serviceContainer) && $serviceContainer !== '') {
            /** @phpstan-ignore return.type */
            return require $serviceContainer;
        }

        return null;
    }

    private static function emit(ResponseInterface $response): void
    {
        if (! $response->hasHeader('Content-Disposition') && ! $response->hasHeader('Content-Range')) {
            (new SapiEmitter())->emit($response);
            return;
        }

        (new SapiStreamEmitter())->emit($response);
    }
}
