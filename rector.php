<?php
// rector.php

declare(strict_types=1);

use Rector\BearSunday\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RayDiNamedAnnotationRector::class);
};
