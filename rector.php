<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\CodingStyle\Rector\FuncCall\FunctionFirstClassCallableRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
    )
    ->withAttributesSets(phpunit: true)
    ->withComposerBased(phpunit: true)
    ->withImportNames()
    ->withFluentCallNewLine()
    ->withRules([
        ArraySpreadInsteadOfArrayMergeRector::class,
        FunctionFirstClassCallableRector::class,
        StaticArrowFunctionRector::class,
    ])
    ->withSkip([
        EncapsedStringsToSprintfRector::class,
        PreferPHPUnitThisCallRector::class,
    ])
    // ->withDeadCodeLevel(100)
    // ->withCodeQualityLevel(100)
    ->withTypeCoverageLevel(10)
;
