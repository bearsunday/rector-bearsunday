# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Rector extension that provides upgrade rules for BEAR.Sunday framework:

1. **Annotation to Attribute migration** - Converting docblock annotations to PHP 8 attributes
2. **Dependency Injection refactoring** - Converting setter/trait injection to constructor injection
3. **Return type modernization** - Converting `ResourceObject` return types to `static`

## Development Commands

### Running Tests

```bash
composer test
# OR
./vendor/bin/phpunit rules-tests
```

This runs PHPUnit tests located in `rules-tests/` directory.

### Running Rector on Target Code

For users of this package (not development of the package itself):

```bash
# Dry-run mode (preview changes)
./vendor/bin/rector -c ./rector-bearsunday.php --dry-run

# Apply changes
./vendor/bin/rector -c ./rector-bearsunday.php
```

Note: When installed via `composer-bin-plugin`, the path may be:
```bash
./vendor-bin/rector/vendor/bin/rector -c ./rector-bearsunday.php
```

## Architecture

### Directory Structure

- `rules/` - Rector rule implementations
  - `RayDiNamedAnnotation/Rector/ClassMethod/RayDiNamedAnnotationRector.php` - Main rule class
- `rules-tests/` - PHPUnit test cases following Rector's testing conventions
  - `RayDiNamedAnnotation/Rector/ClassMethod/RayDiNamedAnnotationRector/`
    - `RayDiNamedAnnotationRectorTest.php` - Test case
    - `Fixture/*.php.inc` - Test fixtures (before/after pairs separated by `-----`)
    - `Fake/*.php` - Fake classes used in tests
    - `config/configured_rule.php` - Test configuration

### Available Rector Rules

| Rule | Purpose |
|------|---------|
| `RayDiNamedAnnotationRector` | `@Named("a=foo")` on method → `#[Named('foo')]` on parameters |
| `SetterToConstructorInjectionRector` | `#[Inject]` setter → constructor injection |
| `TraitToConstructorInjectionRector` | `use ResourceInject` → constructor injection |
| `ResourceObjectReturnTypeRector` | `: ResourceObject` / `: self` → `: static` |

### How RayDiNamedAnnotationRector Works

The rule transforms Ray.Di's `@Named` annotations from method-level to parameter-level attributes:

**Input (annotation on method):**
```php
/**
 * @Named("a=foo, b=bar")
 */
public function __construct(int $a, int $b)
```

**Output (attributes on parameters):**
```php
public function __construct(#[Named('foo')] int $a, #[Named('bar')] int $b)
```

**Implementation details:**
1. Extends `AbstractRector` from Rector core
2. Targets `ClassMethod` nodes via `getNodeTypes()`
3. In `refactor()`:
   - Extracts `@Named` annotation from method docblock
   - Parses the name string (format: `key=value,key=value`)
   - Adds `#[Named]` attributes to matching constructor parameters
   - Removes the method-level `@Named` annotation from docblock

### Test Structure

Tests use Rector's fixture-based approach:
- Fixtures are `.php.inc` files in `rules-tests/.../Fixture/` directory
- Each fixture contains "before" code, separator (`-----`), and "after" code
- Test class extends `AbstractRectorTestCase` and uses `yieldFilesFromDirectory()` data provider
- Configuration file in `config/configured_rule.php` registers the rule

### Key Configuration Files

- `rector.php` - Sample configuration showing how users configure this package with all BEAR.Sunday and Ray.Di annotations
- `rector-recipe.php` - Recipe file used for code generation (via `bin/rector generate`)
- `composer.json` - Defines PSR-4 autoloading: `Rector\BearSunday\` → `rules/`

## Dependencies

- Requires PHP 8.2+
- Requires Rector v2.0+
- Depends on `koriym/attributes` and `ray/di` for attribute/annotation classes
- Dev dependencies include BEAR.Sunday packages for testing annotation classes

## Important Notes

- This package uses Rector 2.x API (see below for key differences from 0.12)
- The main use case is migrating BEAR.Sunday applications from annotations to PHP 8.0+ attributes
- Branch: `1.x` is the main development branch

## Rector 2.x API Changes (from 0.12)

### Key Changes in Custom Rules

1. **Namespace Changes**: `Rector\Core\` → `Rector\`
   - `Rector\Core\Rector\AbstractRector` → `Rector\Rector\AbstractRector`
   - `Rector\PhpAttribute\Printer\PhpAttributeGroupFactory` → `Rector\PhpAttribute\NodeFactory\PhpAttributeGroupFactory`

2. **Dependency Injection**: All dependencies must be explicitly injected via constructor
   - `PhpDocInfoFactory` - for creating/accessing PhpDoc information
   - `DocBlockUpdater` - for updating node docblocks after modification
   - No automatic property injection from parent class

3. **Configuration API**: `RectorConfig::configure()` fluent API
   ```php
   return RectorConfig::configure()
       ->withPaths([...])
       ->withRules([...])
       ->withConfiguredRule(...);
   ```

4. **Test Changes**:
   - `AbstractRectorTestCase::doTestFile(string $filePath)` instead of `doTestFileInfo(SmartFileInfo)`
   - `yieldFilesFromDirectory()` returns `Iterator<string>` instead of `Iterator<SmartFileInfo>`
   - `provideData()` must be static

5. **Optional Methods**: `getRuleDefinition()` is now optional for custom rules
