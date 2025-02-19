<?php

declare(strict_types = 1);

namespace TheNoFramework;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class ApplicationRunnerTest extends TestCase
{
    public function testRunWithoutServiceContainerWithoutMiddlewares()
    {
        $applicationRunner = new ApplicationRunner();

        $requestText = 'TheNoFramework';
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn(new StreamMock($requestText));

        $applicationResponse = $applicationRunner->run(
            SampleRequestHandler::class,
            $request
        );

        $this->assertSame($requestText, (string) $applicationResponse->getBody());
    }

    public function testRunWithoutServiceContainerWithMiddlewares()
    {
        $applicationRunner = new ApplicationRunner();

        $requestText = 'TheNoFramework';
        $middlewareText = 'Middleware';
        $anotherMiddlewareText = 'AnotherMiddleware';
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn(new StreamMock($requestText));

        $applicationResponse = $applicationRunner->run(
            SampleRequestHandler::class,
            $request,
            [
                SampleMiddleware::class,
                AnotherSampleMiddleware::class,
            ]
        );

        $this->assertSame(
            $requestText.$middlewareText.$anotherMiddlewareText,
            (string) $applicationResponse->getBody()
        );
    }

    public function testRunWithServiceContainerWithoutMiddlewares()
    {
        $originalResponse = new ResponseMock();
        $applicationRunner = new ApplicationRunner(
            new ServiceContainerMock([
                SampleRequestHandlerWithDependencies::class => new SampleRequestHandlerWithDependencies($originalResponse),
            ])
        );

        $requestText = 'TheNoFramework';
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn(new StreamMock($requestText));

        $applicationResponse = $applicationRunner->run(
            SampleRequestHandlerWithDependencies::class,
            $request
        );

        $this->assertSame($requestText, (string) $applicationResponse->getBody());
        $this->assertSame($originalResponse, $applicationResponse);
    }

    public function testRunWithServiceContainerWithMiddlewares()
    {
        $requestText = 'TheNoFramework';
        $middlewareText = 'MiddlewareFromServiceContainer';
        $anotherMiddlewareText = 'AnotherMiddlewareFromServiceContainer';

        $originalResponse = new ResponseMock();
        $applicationRunner = new ApplicationRunner(
            new ServiceContainerMock([
                SampleRequestHandlerWithDependencies::class => new SampleRequestHandlerWithDependencies($originalResponse),
                SampleMiddleware::class => new SampleMiddleware($middlewareText),
                AnotherSampleMiddleware::class => new AnotherSampleMiddleware($anotherMiddlewareText),
            ])
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn(new StreamMock($requestText));

        $applicationResponse = $applicationRunner->run(
            SampleRequestHandlerWithDependencies::class,
            $request,
            [
                SampleMiddleware::class,
                AnotherSampleMiddleware::class,
            ]
        );

        $this->assertSame(
            $requestText.$middlewareText.$anotherMiddlewareText,
            (string) $applicationResponse->getBody()
        );
        $this->assertSame($originalResponse, $applicationResponse);
    }
}
