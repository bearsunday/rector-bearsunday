# Rector Rules for BEAR.Sunday

The [rector/rector](http://github.com/rectorphp/rector) rules for BEAR.Sunday.

## Install

```bash
composer require rector/bear-sunday 1.x-dev --dev
```

## Use Sets


```php
<?php
// rector.php

declare(strict_types=1);

use Rector\BearSunday\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(RayDiNamedAnnotationRector::class);
};
```

## Rules

### RayDiNamedAnnotationRector

Change `@Named` annotation in method to `#[Named]` attribute in parameter.

- class: [`RayDiNamedAnnotationRector`](rules/RayDiNamedAnnotation/Rector/ClassMethod/RayDiNamedAnnotationRector.php)

```diff
class SomeClass
{
    /**
     * @Named("a=foo, b=bar")
-    * @Named("a=foo, b=bar")
     * @Foo
     */
-    public function __construct(int $a, int $b)
+    public function __construct(#[Named('foo') int $a, #[Named('bar') int $b)
    {
    }
```
