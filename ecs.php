<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])

    // add sets - group of rules
   ->withPreparedSets(
        psr12: true,
        common: true,
        strict: true,
        cleanCode: true
    )

    ;
