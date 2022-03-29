<?php

declare(strict_types=1);

use Rector\BearSunday\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector;
use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

require __DIR__ . '/vendor/autoload.php';

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src'
    ]);

    $services = $containerConfigurator->services();
    $services->set(RayDiNamedAnnotationRector::class);
};
