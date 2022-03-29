<?php

declare (strict_types=1);
namespace Rector\RayNamedAnnotation\Rector\Class_;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**

* @see \Rector\Tests\RayNamedAnnotation\Rector\Class_\RayNamedAnnotationRector\RayNamedAnnotationRectorTest
*/
final class RayNamedAnnotationRector extends \Rector\Core\Rector\AbstractRector
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
/**
     * @Foo
     */
    public function __construct(#[Named('foo') int $a, #[Named('bar') int $b)
    {
    }
CODE_SAMPLE
)]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return array(\PhpParser\Node\Stmt\Class_::class, \PhpParser\Node\Stmt\ClassMethod::class, \PhpParser\Node\Stmt\Property::class, \PhpParser\Node\Stmt\Interface_::class, \PhpParser\Node\Param::class, \PhpParser\Node\Stmt\Function_::class);
    }
    /**
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\Interface_|\PhpParser\Node\Param|\PhpParser\Node\Stmt\Function_ $node
     */
    public function refactor(\PhpParser\Node $node) : ?\PhpParser\Node
    {
        // change the node
        return $node;
    }
}
