# Rector Rules for BEAR.Sunday

The [rector/rector](http://github.com/rectorphp/rector) rules for BEAR.Sunday.

## Install

```bash
composer require bearsunday/rector-bearsunday 1.x-dev --dev
```

## Use Sets

```php
<?php
// rector.php
use Rector\BearSunday\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector;
use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

require __DIR__ . '/vendor/autoload.php';

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    $services->set(RayDiNamedAnnotationRector::class);
};
```

See [Auto Import Names](https://github.com/rectorphp/rector/blob/main/docs/auto_import_names.md) for `Option::AUTO_IMPORT_NAME`.

## Rules

### RayDiNamedAnnotationRector

Change `@Named` annotation in method to `#[Named]` attribute in parameter.

- class: [`RayDiNamedAnnotationRector`](rules/RayDiNamedAnnotation/Rector/ClassMethod/RayDiNamedAnnotationRector.php)

```diff
class SomeClass
{
    /**
-    * @Named("a=foo, b=bar")
     * @Foo
     */
-    public function __construct(int $a, int $b)
+    public function __construct(#[Named('foo') int $a, #[Named('bar') int $b)
    {
    }
```

## See Also

* [AnnotationToAttributeRector](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#annotationtoattributerector)
