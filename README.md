# Rector Rules for BEAR.Sunday

Rector rules to migrate BEAR.Sunday and Ray.Di applications from annotations to PHP 8 attributes.

## Requirements

- PHP 8.2+
- Rector 2.0+

## Installation

```bash
composer require --dev bearsunday/rector-bearsunday
```

## Usage

Run Rector using the provided configuration file:

```bash
# Preview changes
vendor/bin/rector process src tests -c vendor/bearsunday/rector-bearsunday/rector.php --dry-run

# Apply changes
vendor/bin/rector process src tests -c vendor/bearsunday/rector-bearsunday/rector.php
```

## Rules

### RayDiNamedAnnotationRector

Converts `@Named` annotations on methods to `#[Named]` attributes on constructor parameters.

**Before:**
```php
class SomeClass
{
    /**
     * @Named("a=foo, b=bar")
     */
    public function __construct(int $a, int $b)
    {
    }
}
```

**After:**
```php
class SomeClass
{
    public function __construct(
        #[Named('foo')] int $a,
        #[Named('bar')] int $b
    ) {
    }
}
```

### AnnotationToAttributeRector

Converts BEAR.Sunday and Ray.Di annotations to PHP 8 attributes.

The provided `rector.php` configuration includes rules for:

**Ray.Di:**
- `@Inject` → `#[Inject]`
- `@Named` → `#[Named]`
- `@PostConstruct` → `#[PostConstruct]`
- `@Assisted` → `#[Assisted]`
- `@Set` → `#[Set]`
- `@Qualifier` → `#[Qualifier]`

**Ray.AuraSqlModule:**
- `@ReadOnlyConnection` → `#[ReadOnlyConnection]`
- `@WriteConnection` → `#[WriteConnection]`
- `@Transactional` → `#[Transactional]`

**Ray.PsrCacheModule:**
- `@CacheNamespace` → `#[CacheNamespace]`
- `@Local` → `#[Local]`
- `@Shared` → `#[Shared]`
- `@CacheDir` → `#[CacheDir]`

**Ray.WebContextParam:**
- `@CookieParam` → `#[CookieParam]`
- `@EnvParam` → `#[EnvParam]`
- `@FilesParam` → `#[FilesParam]`
- `@FormParam` → `#[FormParam]`
- `@QueryParam` → `#[QueryParam]`
- `@ServerParam` → `#[ServerParam]`

**Ray.QueryModule:**
- `@Query` → `#[Query]`

**Ray.RoleModule:**
- `@RequiresRoles` → `#[RequiresRoles]`

**BEAR.Resource:**
- `@AppName` → `#[AppName]`
- `@ContextScheme` → `#[ContextScheme]`
- `@Embed` → `#[Embed]`
- `@ImportAppConfig` → `#[ImportAppConfig]`
- `@Link` → `#[Link]`
- `@OptionsBody` → `#[OptionsBody]`
- `@ResourceParam` → `#[ResourceParam]`

**BEAR.Package:**
- `@ReturnCreatedResource` → `#[ReturnCreatedResource]`

**BEAR.QueryRepository:**
- `@Cacheable` → `#[Cacheable]`
- `@Purge` → `#[Purge]`
- `@Refresh` → `#[Refresh]`

**BEAR.Accept:**
- `@Available` → `#[Available]`
- `@Produces` → `#[Produces]`

### SetterToConstructorInjectionRector

Converts setter injection with `#[Inject]` to constructor injection.

**Before:**
```php
class SomeClass
{
    private FooInterface $foo;

    #[Inject]
    public function setFoo(FooInterface $foo): void
    {
        $this->foo = $foo;
    }
}
```

**After:**
```php
class SomeClass
{
    public function __construct(
        private readonly FooInterface $foo
    ) {
    }
}
```

### TraitToConstructorInjectionRector

Converts trait-based injection to constructor injection.

**Supported traits:**
- `BEAR\Resource\ResourceInject` → `ResourceInterface $resource`
- `BEAR\Sunday\Inject\ResourceInject` → `ResourceInterface $resource`
- `Ray\Di\InjectorInject` → `InjectorInterface $injector`

**Before:**
```php
use BEAR\Resource\ResourceInject;

class SomeClass
{
    use ResourceInject;
}
```

**After:**
```php
use BEAR\Resource\ResourceInterface;

class SomeClass
{
    public function __construct(
        private readonly ResourceInterface $resource
    ) {
    }
}
```

### ResourceObjectReturnTypeRector

Converts `ResourceObject` and `self` return types to `static` in resource methods.

**Before:**
```php
class Article extends ResourceObject
{
    public function onGet(int $id): ResourceObject
    {
        return $this;
    }
}
```

**After:**
```php
class Article extends ResourceObject
{
    public function onGet(int $id): static
    {
        return $this;
    }
}
```

## See Also

- [Rector - Instant PHP Upgrades](https://getrector.com/)
- [AnnotationToAttributeRector](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#annotationtoattributerector)
