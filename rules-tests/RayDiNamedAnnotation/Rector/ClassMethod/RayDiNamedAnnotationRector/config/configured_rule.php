<?php

declare (strict_types=1);

use Rector\BearSunday\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([
        RayDiNamedAnnotationRector::class,
    ]);
