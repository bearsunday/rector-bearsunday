<?php

declare(strict_types=1);

use Rector\BearSunday\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;

return RectorConfig::configure()
    // Update @Named method annotations to #[Named] parameter attributes
    ->withRules([
        RayDiNamedAnnotationRector::class,
    ])
    ->withConfiguredRule(AnnotationToAttributeRector::class, [
        // ray/aura-sql-module
        new AnnotationToAttribute('Ray\AuraSqlModule\Annotation\ReadOnlyConnection'),
        new AnnotationToAttribute('Ray\AuraSqlModule\Annotation\WriteConnection'),
        new AnnotationToAttribute('Ray\AuraSqlModule\Annotation\Transactional'),
        // ray/di
        new AnnotationToAttribute('Ray\Di\Di\Assisted'),
        new AnnotationToAttribute('Ray\Di\Di\Inject'),
        new AnnotationToAttribute('Ray\Di\Di\Named'),
        new AnnotationToAttribute('Ray\Di\Di\PostConstruct'),
        new AnnotationToAttribute('Ray\Di\Di\Set'),
        new AnnotationToAttribute('Ray\Di\Di\Qualifier'),
        // ray/psr-cache-module
        new AnnotationToAttribute('Ray\PsrCacheModule\Annotation\CacheNamespace'),
        new AnnotationToAttribute('Ray\PsrCacheModule\Annotation\Local'),
        new AnnotationToAttribute('Ray\PsrCacheModule\Annotation\Shared'),
        new AnnotationToAttribute('Ray\PsrCacheModule\Annotation\CacheDir'),
        new AnnotationToAttribute('BEAR\Resource\Annotation\AppName'),
        new AnnotationToAttribute('BEAR\Resource\Annotation\ContextScheme'),
        // ray/role-module
        new AnnotationToAttribute('Ray\RoleModule\Annotation\RequiresRoles'),
        // ray/web-context
        new AnnotationToAttribute('Ray\WebContextParam\Annotation\CookieParam'),
        new AnnotationToAttribute('Ray\WebContextParam\Annotation\EnvParam'),
        new AnnotationToAttribute('Ray\WebContextParam\Annotation\FilesParam'),
        new AnnotationToAttribute('Ray\WebContextParam\Annotation\FormParam'),
        new AnnotationToAttribute('Ray\WebContextParam\Annotation\QueryParam'),
        new AnnotationToAttribute('Ray\WebContextParam\Annotation\ServerParam'),
        new AnnotationToAttribute('BEAR\Package\Annotation\ReturnCreatedResource'),

        // bear/query-module
        new AnnotationToAttribute('Ray\Query\Annotation\Query'),
        // bear/query-repository
        new AnnotationToAttribute('BEAR\RepositoryModule\Annotation\Cacheable'),
        new AnnotationToAttribute('BEAR\RepositoryModule\Annotation\Purge'),
        new AnnotationToAttribute('BEAR\RepositoryModule\Annotation\Refresh'),
        // bear/resource
        new AnnotationToAttribute('BEAR\Resource\Annotation\Embed'),
        new AnnotationToAttribute('BEAR\Resource\Annotation\ImportAppConfig'),
        new AnnotationToAttribute('BEAR\Resource\Annotation\Link'),
        new AnnotationToAttribute('BEAR\Resource\Annotation\OptionsBody'),
        new AnnotationToAttribute('BEAR\Resource\Annotation\ResourceParam'),
    ]);
