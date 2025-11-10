<?php

declare (strict_types=1);

namespace Rector\BearSunday\RayDiNamedAnnotation\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use Ray\Di\Di\Named;
use Rector\BetterPhpDocParser\PhpDoc\DoctrineAnnotationTagValueNode;
use Rector\BetterPhpDocParser\PhpDoc\StringNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTagRemover;
use Rector\Comments\NodeDocBlock\DocBlockUpdater;
use Rector\Rector\AbstractRector;
use Rector\PhpAttribute\NodeFactory\PhpAttributeGroupFactory;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function array_merge;
use function assert;
use function explode;
use function is_string;
use function substr;
use function trim;

/**
 * @see \Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\RayDiNamedAnnotationRectorTest
 */
final class RayDiNamedAnnotationRector extends AbstractRector
{
    public function __construct(
        private PhpAttributeGroupFactory $attributeGroupFactory,
        private PhpDocTagRemover $phpDocTagRemover,
        private PhpDocInfoFactory $phpDocInfoFactory,
        private DocBlockUpdater $docBlockUpdater
    ){}

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
        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        $doctrineTagValueNode = $phpDocInfo->getByAnnotationClass(Named::class);
        if (! $doctrineTagValueNode instanceof DoctrineAnnotationTagValueNode) {
            return null;
        }
        $silentValue = $doctrineTagValueNode->getSilentValue();
        if ($silentValue === null) {
            return null;
        }

        // Get the string value from StringNode or directly
        $value = $silentValue->value;
        $nameString = $value instanceof StringNode ? $value->value : (string) $value;

        $names = $this->parseName($nameString);
        $hasChanged = false;

        foreach ($node->params as $param) {
            $varName = $param->var->name;
            if (! isset($names[$varName])) {
                continue;
            }
            $attrGroupsFromNamedAnnotation = $this->attributeGroupFactory->createFromClassWithItems(Named::class, [$names[$varName]]);
            $param->attrGroups = array_merge($param->attrGroups, [$attrGroupsFromNamedAnnotation]);
            $hasChanged = true;
        }

        if (! $hasChanged) {
            return null;
        }

        // Remove the @Named annotation from the docblock after processing all parameters
        $phpDocInfo->removeByName('@Named');

        // Update the node with the modified PhpDocInfo
        $this->docBlockUpdater->updateRefactoredNodeWithPhpDocInfo($node);

        return $node;
    }

    /**
     * @return array<string, string>
     */
    private function parseName(string $name): array
    {
        $names = [];
        $keyValues = explode(',', $name);
        foreach ($keyValues as $keyValue) {
            $exploded = explode('=', $keyValue);
            if (isset($exploded[1])) {
                [$key, $value] = $exploded;
                assert(is_string($key));
                if (isset($key[0]) && $key[0] === '$') {
                    $key = substr($key, 1);
                }

                $names[trim($key)] = trim($value);
            }
        }

        return $names;
    }
}
