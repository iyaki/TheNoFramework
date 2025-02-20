# TheNoFramework

The goal of this library is to provide a simple way to implement PSR-15 compliant controllers (request handlers) in small projects that wouldn't take advantage of all the features of a bigger framework.

This package takes advantage of the PHP `auto_prepend_file` directive to avoid the need of a index.php acting as a front controller of your application, using TheNoFramework every controller is an entry point.

Features provided in this package:

- A PSR-15 request dispatcher (using [laminas-diactoros](https://github.com/laminas/laminas-diactoros)).
- Support to add PSR-15 middlewares.
- A emitter (using [laminas-httphandlerrunner](https://github.com/laminas/laminas-httphandlerrunner)) for the PSR-7 responses.
- **No** routing system provided.
- **No** exception handling provided.
- **No** authentication provided.
- **No** ORM or DBAL provided.
- **No** service container provided.

If you decide to use or implement a PSR-11 container (you probably should) this library can use it to allow the instantiation of complex request handlers and middlewares.

## Install

Via composer

```shell
composer require iyaki/the-no-framework
```

## Configuration

This library relies on a environment variables to discover and use a PSR-11 compliant service container:

The environment variables must be named `SERVICE_CONTAINER_WRAPPER` and store the path to the PSR-11 service container wrapper (a php file that returns an instance of `Psr\Container\ContainerInterface`).

If this variable is not provided the library will not have access to the service container and will work only with simple (no constructor arguments) request handlers.

## Usage

bootstrap.php

```php
<?php
require __DIR__ . '/vendor/autoload.php';
```

php.ini

```ini
auto_prepend_file = /full/path/to/bootstrap.php
```

or for PHP built-in web server

```shell
php -d auto_prepend_file=path/to/bootstrap.php -S localhost:8080 -t public/
```

MyRequestHandler.php

```php
<?php

declare(strict_types = 1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class MyRequestHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /* Do stuffs */
    }
}
\TheNoFramework\ApplicationWrapper::run(MyRequestHandler::class);
```

## Examples

Yoy can see TheNoFramework working in [this example project](https://github.com/iyaki/TheNoFramework-petstore-example)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

```shell
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
