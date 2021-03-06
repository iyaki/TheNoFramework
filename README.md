# TheNoFramework

The goal of this library is to provide a simple way to implement PSR-15 compliant controllers (request handlers) in small projects that wouldn't take advantage of all the features of a bigger framework.

This package takes advantage of the PHP 7.4 preloading feature to avoid the need of a index.php acting as wrapper of your application, using TheNoFramework every controller is an entry point. 

Features provided in this package:
- A PSR-15 request dispatcher (using [laminas-diactoros](https://github.com/laminas/laminas-diactoros)).
- Support to add PSR-15 middlewares.
- A emitter (using [laminas-httphandlerrunner](https://github.com/laminas/laminas-httphandlerrunner)) for the PSR-7 responses.
- No routing system provided.
- No exception handling provided.
- No authentication provided.
- No ORM or DBAL provided.
- No service container provided. 

If you decide to use or implement a PSR-11 container (you probably should do it) this library will use it to allow the instantiation of complex request handlers and middlewares.

## Install

Via Composer

```shell
$ composer require iyaki/the-no-framework
```

## Configuration

This library relies on 2 environment variables to implement the service container (PSR-11) and the composer autoloading:
- `SERVICE_CONTAINER_WRAPPER`: This environment variable should store the path to the PSR-11 service container wrapper (a php file that returns an instance of `Psr\Container\ContainerInterface`).  
If this variable is not provided the library will not have access to the service container and will work only with simple (no constructor arguments) classes.
- `AUTOLOAD_PATH`: This one should store the path to the `autoload.php` file generated by composer. If this variable is not provided the default path (vendor/autoload.php) will be used.

## Usage

php.ini
```ini
opcache.preload = preload.php
```

preload.php
```php
<?php

$autoload = require __DIR__.'/vendor/autoload.php';

$classesToPreload = [
    'TheNoFramework\ApplicationWrapper',
    'Psr\Http\Server\RequestHandlerInterface',
    /* Other classes you want to preload */
];

foreach ($classesToPreload as $classToPreload) {
    require $autoload->findFile($classToPreload);
}

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

```bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
