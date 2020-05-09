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

    /**
     * Runs the given request handler and middlewares
     *
     * @param string $requestHandlerClass
     * @param MiddlewareInterface[] $middlewares
     * @return void
     */
    public static function run(string $requestHandlerClass, array $middlewares): void
    {
        self::loadAutoLoader();

        $applicationRunner = new ApplicationRunner(self::getServiceContainer());

        $serverRequest = ServerRequestFactory::fromGlobals();

        $response = $applicationRunner->run($requestHandlerClass, $serverRequest, $middlewares);

        self::emit($response);
    }

    private static function loadAutoLoader(): void
    {
        if (!empty($_ENV['AUTOLOAD_PATH'])) {
            require $_ENV['AUTOLOAD_PATH'];
            return;
        }

        $defaultComposerAutoloaderPath = __DIR__.'/../../autoload.php';
        if (file_exists($defaultComposerAutoloaderPath)) {
            require $defaultComposerAutoloaderPath;
        }
    }

    private static function getServiceContainer(): ?ContainerInterface
    {
        return empty($_ENV['SERVICE_CONTAINER_WRAPPER']) ? null : $_ENV['SERVICE_CONTAINER_WRAPPER'];
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
