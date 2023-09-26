# Rector Rules for BEAR.Sunday

The [rector/rector](http://github.com/rectorphp/rector) rules for BEAR.Sunday.

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
+    public function __construct(#[Named('foo')] int $a, #[Named('bar')] int $b)
    {
    }
```

## Install

```bash
composer require bearsunday/rector-bearsunday 1.x-dev --dev
```

## How to convert annotation-based Ray or BEAR applications to attributes


1. rectorのインストール

rector-bearsundayの実行にはrector v0.12が必要です。特定のバージョンのrectorをインストールするために`bamarni/composer-bin-plugin`をインストールします。
インストールした後は設定ファイルをコピーします。

Running rector-bearsunday requires rector v0.12. Install `bamarni/composer-bin-plugin` to install a specific version of rector.
After installation, copy the configuration file.

```php
composer require --dev bamarni/composer-bin-plugin
composer bin rector require bearsunday/rector-bearsunday 1.x-dev --dev
cp ./vendor-bin/rector/vendor/bearsunday/rector-bearsunday/rector.php rector-bearsunday.php
```

` rector-bearsunday.php`設定ファイルで変換するアトリビュートや適用するディレクトリを指定します。
Specify the attributes to be converted and the directories to be applied in the `rector-bearsunday.php` configuration file.

```php
<?php

declare(strict_types=1);

use BEAR\Package\Annotation\ReturnCreatedResource;
use BEAR\RepositoryModule\Annotation\Cacheable;
use BEAR\RepositoryModule\Annotation\Purge;
use BEAR\RepositoryModule\Annotation\Refresh;
use BEAR\Resource\Annotation\AppName;
use BEAR\Resource\Annotation\ContextScheme;
use BEAR\Resource\Annotation\Embed;
use BEAR\Resource\Annotation\ImportAppConfig;
use BEAR\Resource\Annotation\Link;
use BEAR\Resource\Annotation\OptionsBody;
use BEAR\Resource\Annotation\ResourceParam;
use Ray\AuraSqlModule\Annotation\ReadOnlyConnection;
use Ray\AuraSqlModule\Annotation\Transactional;
use Ray\AuraSqlModule\Annotation\WriteConnection;
use Ray\Di\Di\Assisted;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\Di\Di\PostConstruct;
use Ray\Di\Di\Qualifier;
use Ray\Di\Di\Set;
use Ray\PsrCacheModule\Annotation\CacheDir;
use Ray\PsrCacheModule\Annotation\CacheNamespace;
use Ray\PsrCacheModule\Annotation\Local;
use Ray\PsrCacheModule\Annotation\Shared;
use Ray\Query\Annotation\Query;
use Ray\RoleModule\Annotation\RequiresRoles;
use Ray\WebContextParam\Annotation\CookieParam;
use Ray\WebContextParam\Annotation\EnvParam;
use Ray\WebContextParam\Annotation\FilesParam;
use Ray\WebContextParam\Annotation\FormParam;
use Ray\WebContextParam\Annotation\QueryParam;
use Ray\WebContextParam\Annotation\ServerParam;
use Rector\BearSunday\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor-bin/rector/vendor/autoload.php';

return static function (RectorConfig $rectorConfig): void {
    $services = $rectorConfig->services();
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // Update @Named method annotations to #[Named] parameter attributes
    $services->set(RayDiNamedAnnotationRector::class);

    $rectorConfig->ruleWithConfiguration(AnnotationToAttributeRector::class, [
        // ray/aura-sql-module
        new AnnotationToAttribute(ReadOnlyConnection::class),
        new AnnotationToAttribute(WriteConnection::class),
        new AnnotationToAttribute(Transactional::class),
        // ray/di
        new AnnotationToAttribute(Assisted::class),
        new AnnotationToAttribute(Inject::class),
        new AnnotationToAttribute(Named::class),
        new AnnotationToAttribute(PostConstruct::class),
        new AnnotationToAttribute(Set::class),
        new AnnotationToAttribute(Qualifier::class),
        // ray/psr-cache-module
        new AnnotationToAttribute(CacheNamespace::class),
        new AnnotationToAttribute(Local::class),
        new AnnotationToAttribute(Shared::class),
        new AnnotationToAttribute(CacheDir::class),
        new AnnotationToAttribute(AppName::class),
        new AnnotationToAttribute(ContextScheme::class),
        // ray/role-module
        new AnnotationToAttribute(RequiresRoles::class),
        // ray/web-context
        new AnnotationToAttribute(CookieParam::class),
        new AnnotationToAttribute(EnvParam::class),
        new AnnotationToAttribute(FilesParam::class),
        new AnnotationToAttribute(FormParam::class),
        new AnnotationToAttribute(QueryParam::class),
        new AnnotationToAttribute(ServerParam::class),
        new AnnotationToAttribute(ReturnCreatedResource::class),

        // bear/query-module
        new AnnotationToAttribute(Query::class),
        // bear/query-repository
        new AnnotationToAttribute(Cacheable::class),
        new AnnotationToAttribute(Purge::class),
        new AnnotationToAttribute(Refresh::class),
        // bear/resource
        new AnnotationToAttribute(Embed::class),
        new AnnotationToAttribute(ImportAppConfig::class),
        new AnnotationToAttribute(Link::class),
        new AnnotationToAttribute(OptionsBody::class),
        new AnnotationToAttribute(ResourceParam::class),
};
```
## 実行

`composer-bin-plugin`の設定によってrectorの実行パスは変わります。

The execution path of rector depends on the setting of `composer-bin-plugin`.

```
./vendor/bin/rector -c ./rector-bearsunday.php --dry-run
```
Or
```
./vendor-bin/rector/vendor/bin/rector -c ./rector-bearsunday.php --dry-run
```

## See Also

* [AnnotationToAttributeRector](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#annotationtoattributerector)
