<?php

declare(strict_types = 1);

namespace TheNoFramework;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class ApplicationRunnerTest extends TestCase
{
    public function __construct()
    {
        parent::__construct();
    }

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
                new SampleMiddleware($middlewareText),
                new AnotherSampleMiddleware($anotherMiddlewareText),
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
        $originalResponse = new ResponseMock();
        $applicationRunner = new ApplicationRunner(
            new ServiceContainerMock([
                SampleRequestHandlerWithDependencies::class => new SampleRequestHandlerWithDependencies($originalResponse),
            ])
        );

        $requestText = 'TheNoFramework';
        $middlewareText = 'Middleware';
        $anotherMiddlewareText = 'AnotherMiddleware';
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn(new StreamMock($requestText));

        $applicationResponse = $applicationRunner->run(
            SampleRequestHandlerWithDependencies::class,
            $request,
            [
                new SampleMiddleware($middlewareText),
                new AnotherSampleMiddleware($anotherMiddlewareText),
            ]
        );

        $this->assertSame(
            $requestText.$middlewareText.$anotherMiddlewareText,
            (string) $applicationResponse->getBody()
        );
        $this->assertSame($originalResponse, $applicationResponse);
    }
}
