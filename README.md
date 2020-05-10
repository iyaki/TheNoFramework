# TheNoFramework

This is a proof of concept of how to use object oriented PSR-15 compliant controllers almost without a framework.

## Install

Via Composer

``` shell
$ composer require iyaki/the-no-framework
```

## Usage

php.ini
``` ini
opcache.preload = preload.php
```

preload.php
```php
<?php

$autoload = require __DIR__.'/vendor/autoload.php';

$classesToPreload = [
    'TheNoFramework\ApplicationWrapper',
    /* Other classes you want to preload */
];

foreach ($classesToPreload as $classToPreload) {
    require $autoload->findFile($classToPreload);
}

```

MyRequestHandler.php
``` php
<?php

declare(strict_types = 1);

namespace TheNoFramework;

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

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.
IMPORTANT: Until this project reaches a 1.0 release, breaking changes will be released without prior notice or deprecation.

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
