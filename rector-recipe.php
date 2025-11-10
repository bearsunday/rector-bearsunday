<?php

declare (strict_types=1);

use PhpParser\Node\Stmt\ClassMethod;
use Rector\RectorGenerator\Provider\RectorRecipeProvider;
use Rector\RectorGenerator\ValueObject\Option;
use Rector\Config\RectorConfig;

// run "bin/rector generate" to a new Rector basic schema + tests from this config
return RectorConfig::configure()
    ->withConfiguredRule(RectorRecipeProvider::class, [
        // [REQUIRED]
        // [RECTOR CORE CONTRIBUTION - REQUIRED]
        // package name, basically namespace part in `rules/<package>/src`, use PascalCase
        Option::PACKAGE => 'RayDiNamedAnnotation',
        // name, basically short class name; use PascalCase
        Option::NAME => 'RayDiNamedAnnotationRector',
        // 1+ node types to change, pick from classes here https://github.com/nikic/PHP-Parser/tree/master/lib/PhpParser/Node
        // the best practise is to have just 1 type here if possible, and make separated rule for other node types
        Option::NODE_TYPES => [ClassMethod::class],
        // describe what the rule does
        Option::DESCRIPTION => '"Method @named annotation will changed to be parameter #[Named] attribute"',
        // code before change
        // this is used for documentation and first test fixture
        Option::CODE_BEFORE => <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @Named("a=foo,b=bar")
     * @Foo
     */
    public function __construct(int $a, int $b)
    {
    }
}
CODE_SAMPLE,
        // code after change
        Option::CODE_AFTER => <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @Foo
     */
    public function __construct(#[\Ray\Di\Di\Named('foo')] int $a, #[\Ray\Di\Di\Named('bar')] int $b)
    {
    }
}
CODE_SAMPLE,
    ]);
