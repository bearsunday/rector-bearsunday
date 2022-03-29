<?php

declare (strict_types=1);
namespace Rector\RayDiNamedAnnotation\Rector\ClassMethod;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**

* @see \Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\RayDiNamedAnnotationRectorTest
*/
final class RayDiNamedAnnotationRector extends \Rector\Core\Rector\AbstractRector
{
    public function getRuleDefinition() : \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \Symplify\RuleDocGenerator\ValueObject\RuleDefinition('"Mehtod @named annotation will changed to be parameter #[Named] attribute"', [new \Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
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
, <<<'CODE_SAMPLE'
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
)]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return array(\PhpParser\Node\Stmt\ClassMethod::class);
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     */
    public function refactor(\PhpParser\Node $node) : ?\PhpParser\Node
    {
        // change the node
        return $node;
    }
}
