<?php

declare(strict_types=1);

use Rector\BearSunday\ResourceObject\Rector\ClassMethod\ResourceObjectReturnTypeRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
        ResourceObjectReturnTypeRector::class,
    ]);
