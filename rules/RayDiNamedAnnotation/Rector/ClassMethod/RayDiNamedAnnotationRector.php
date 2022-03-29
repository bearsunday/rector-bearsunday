<?php

declare (strict_types=1);

namespace Rector\BearSunday\RayDiNamedAnnotation\Rector\ClassMethod;

use Doctrine\Common\Annotations\AnnotationReader;
use Koriym\Attributes\AttributeReader;
use Koriym\Attributes\DualReader;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Ray\Di\Di\Named;
use Ray\Di\Name;
use Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Core\Rector\AbstractRector;
use Rector\PhpAttribute\Printer\PhpAttributeGroupFactory;
use ReflectionMethod;
use ReflectionParameter;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function array_merge;

/**
 * @see \Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\RayDiNamedAnnotationRectorTest
 */
final class RayDiNamedAnnotationRector extends AbstractRector
{
    private DualReader $reader;

    public function __construct(
        private PhpAttributeGroupFactory                                      $attributeGroupFactory,
        private PhpDocTagRemover $phpDocTagRemove
    )
    {
        $this->reader = new DualReader(new AnnotationReader(), new AttributeReader());
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('"Mehtod @named annotation will changed to be parameter #[Named] attribute"', [new CodeSample(<<<'CODE_SAMPLE'
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
    public function __construct(#[Named('foo') int $a, #[Named('bar') int $b)
    {
    }
CODE_SAMPLE
        )]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof ClassMethod);
        $class = $node->getAttributes()['parent']->name->name;
        $method = new ReflectionMethod($class, $node->name->name);
        $named = $this->reader->getMethodAnnotation($method, Named::class);
        if (!$named instanceof Named) {
            return null;
        }

        $name = new Name($named->value);
        foreach ($node->params as $param) {
            $namedString = ($name)(new ReflectionParameter([$class, $method->name], $param->var->name));
            if ($namedString === '') {
                continue;
            }
            $attrGroupsFromNamedAnnotation = $this->attributeGroupFactory->createFromClassWithItems(Named::class, [$namedString]);
            $param->attrGroups = array_merge($param->attrGroups, [$attrGroupsFromNamedAnnotation]);

            $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
            $doctrineAnnotationTagValueNode = $phpDocInfo->getByAnnotationClass(Named::class);
            if (! $doctrineAnnotationTagValueNode instanceof DoctrineAnnotationTagValueNode) {
                continue;
            }

            $this->phpDocTagRemove->removeTagValueFromNode($phpDocInfo, $doctrineAnnotationTagValueNode);
        }

        return $node;
    }
}
