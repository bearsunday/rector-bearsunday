<?php

declare(strict_types=1);

use Rector\BearSunday\TraitInjection\Rector\Class_\TraitToConstructorInjectionRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
        TraitToConstructorInjectionRector::class,
    ]);
