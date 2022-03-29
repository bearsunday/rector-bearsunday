<?php

declare (strict_types=1);
namespace Rector\RayDiNamedAnnotation\Rector\ClassMethod;

use Doctrine\Common\Annotations\AnnotationReader;
use Koriym\Attributes\AttributeReader;
use Koriym\Attributes\DualReader;
use PhpParser\Node;
use Ray\Di\Di\Named;
use Ray\Di\Name;
use Rector\PhpAttribute\Printer\PhpAttributeGroupFactory;
use ReflectionMethod;
use function array_merge;

/**

* @see \Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\RayDiNamedAnnotationRectorTest
*/
final class RayDiNamedAnnotationRector extends \Rector\Core\Rector\AbstractRector
{
    private DualReader $reader;
    private PhpAttributeGroupFactory $factory;

    public function __construct(PhpAttributeGroupFactory $factory)
    {
        $this->reader = new DualReader(new AnnotationReader(), new AttributeReader());
        $this->factory = $factory;
    }

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
        return array(\PhpParser\Node\Stmt\ClassMethod::class);
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod $node
     */
    public function refactor(Node $node) : ?\PhpParser\Node
    {
        assert($node instanceof Node\Stmt\ClassMethod);
        $class = $node->getAttributes()['parent']->name->name;
        $method = new ReflectionMethod($class, $node->name->name);
        $named = $this->reader->getMethodAnnotation($method, Named::class);
        assert($named instanceof Named);

        $name = new Name($named->value);
        foreach ($node->params as $param) {
            $namedString = ($name)(new \ReflectionParameter([$class, $method->name], $param->var->name));
            $attrGroupsFromNamedAnnotation = $this->factory->createFromClassWithItems(Named::class, [$namedString]);
            $param->attrGroups = array_merge($param->attrGroups, [$attrGroupsFromNamedAnnotation]);
        }

        // change the node
        return $node;
    }
}
