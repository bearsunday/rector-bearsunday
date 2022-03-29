<?php

declare (strict_types=1);
namespace RectorPrefix20220323;

use PhpParser\Node\Expr\MethodCall;
use Rector\RectorGenerator\Provider\RectorRecipeProvider;
use Rector\RectorGenerator\ValueObject\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
// run "bin/rector generate" to a new Rector basic schema + tests from this config
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    // [REQUIRED]
    $rectorRecipeConfiguration = [
        // [RECTOR CORE CONTRIBUTION - REQUIRED]
        // package name, basically namespace part in `rules/<package>/src`, use PascalCase
        \Rector\RectorGenerator\ValueObject\Option::PACKAGE => 'RayDiNamedAnnotation',
        // name, basically short class name; use PascalCase
        \Rector\RectorGenerator\ValueObject\Option::NAME => 'RayDiNamedAnnotationRector',
        // 1+ node types to change, pick from classes here https://github.com/nikic/PHP-Parser/tree/master/lib/PhpParser/Node
        // the best practise is to have just 1 type here if possible, and make separated rule for other node types
        \Rector\RectorGenerator\ValueObject\Option::NODE_TYPES => [ \PhpParser\Node\Stmt\ClassMethod::class],
        // describe what the rule does
        \Rector\RectorGenerator\ValueObject\Option::DESCRIPTION => '"Mehtod @named annotation will changed to be parameter #[Named] attribute"',
        // code before change
        // this is used for documentation and first test fixture
        \Rector\RectorGenerator\ValueObject\Option::CODE_BEFORE => <<<'CODE_SAMPLE'
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
CODE_SAMPLE
,
        // code after change
        \Rector\RectorGenerator\ValueObject\Option::CODE_AFTER => <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @Foo
     */
    public function __construct(#[\Ray\Di\Di\Named('foo')] int $a, #[\Ray\Di\Di\Named('bar')] int $b)
    {
    }
}
CODE_SAMPLE
,
    ];
    $services->set(\Rector\RectorGenerator\Provider\RectorRecipeProvider::class)->arg('$rectorRecipeConfiguration', $rectorRecipeConfiguration);
};
