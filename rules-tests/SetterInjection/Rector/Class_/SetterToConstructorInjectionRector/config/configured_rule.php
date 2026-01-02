<?php

declare(strict_types=1);

use Rector\BearSunday\SetterInjection\Rector\Class_\SetterToConstructorInjectionRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
        SetterToConstructorInjectionRector::class,
    ]);
