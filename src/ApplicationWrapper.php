<?php

declare(strict_types = 1);

namespace TheNoFramework;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

final class ApplicationWrapper
{
    private const ENV_AUTOLOAD_PATH = 'AUTOLOAD_PATH';

    private const ENV_SERVICE_CONTAINER_WRAPPER = 'SERVICE_CONTAINER_WRAPPER';

    /**
     * Runs the given request handler and middlewares
     *
     * @param string $requestHandlerClass
     * @param string[] $middlewares
     * @return void
     */
    public static function run(string $requestHandlerClass, array $middlewares = []): void
    {
        self::loadAutoLoader();

        $applicationRunner = new ApplicationRunner(self::getServiceContainer());

        $serverRequest = ServerRequestFactory::fromGlobals();

        $response = $applicationRunner->run($requestHandlerClass, $serverRequest, $middlewares);

        self::emit($response);
    }

    private static function loadAutoLoader(): void
    {
        $envComposerAutoloaderPath = getenv(self::ENV_AUTOLOAD_PATH);
        if ($envComposerAutoloaderPath) {
            require $envComposerAutoloaderPath;
            return;
        }

        $defaultComposerAutoloaderPath = __DIR__.'/../../../autoload.php';
        if (file_exists($defaultComposerAutoloaderPath)) {
            require $defaultComposerAutoloaderPath;
        }
    }

    private static function getServiceContainer(): ?ContainerInterface
    {
        $serviceContainer = getenv(self::ENV_SERVICE_CONTAINER_WRAPPER);
        if ($serviceContainer) {
            return require $serviceContainer;
        }
        return null;
    }

    private static function emit(ResponseInterface $response): void
    {
        if (!$response->hasHeader('Content-Disposition')
            && !$response->hasHeader('Content-Range')
        ) {
            (new SapiEmitter())->emit($response);
            return;
        }
        (new SapiStreamEmitter())->emit($response);
    }
}
