<?php

declare(strict_types=1);

namespace Rector\BearSunday\ResourceObject\Rector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function assert;
use function in_array;
use function str_starts_with;

/**
 * Converts ResourceObject return type to static in resource methods
 *
 * @see \Rector\Tests\ResourceObject\Rector\ClassMethod\ResourceObjectReturnTypeRector\ResourceObjectReturnTypeRectorTest
 */
final class ResourceObjectReturnTypeRector extends AbstractRector
{
    private const RESOURCE_METHODS = ['onGet', 'onPost', 'onPut', 'onPatch', 'onDelete'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert ResourceObject return type to static in resource methods', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use BEAR\Resource\ResourceObject;

class Article extends ResourceObject
{
    public function onGet(int $id): ResourceObject
    {
        return $this;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use BEAR\Resource\ResourceObject;

class Article extends ResourceObject
{
    public function onGet(int $id): static
    {
        return $this;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Class_);

        if (! $this->isResourceObjectClass($node)) {
            return null;
        }

        $hasChanged = false;

        foreach ($node->getMethods() as $method) {
            if (! $this->isResourceMethod($method)) {
                continue;
            }

            if ($this->shouldChangeReturnType($method)) {
                $method->returnType = new Identifier('static');
                $hasChanged = true;
            }
        }

        return $hasChanged ? $node : null;
    }

    private function isResourceObjectClass(Class_ $class): bool
    {
        if ($class->extends === null) {
            return false;
        }

        $parentClassName = $class->extends->toString();

        return $parentClassName === 'ResourceObject'
            || $parentClassName === 'BEAR\Resource\ResourceObject';
    }

    private function isResourceMethod(ClassMethod $method): bool
    {
        $methodName = $method->name->toString();

        return in_array($methodName, self::RESOURCE_METHODS, true);
    }

    private function shouldChangeReturnType(ClassMethod $method): bool
    {
        $returnType = $method->returnType;

        if ($returnType === null) {
            return false;
        }

        // Get string representation of return type
        $typeName = '';
        if ($returnType instanceof Name) {
            $typeName = $returnType->toString();
        } elseif ($returnType instanceof Identifier) {
            $typeName = $returnType->name;
        }

        // Already static - no change needed
        if ($typeName === 'static') {
            return false;
        }

        // Should change: ResourceObject, self, or fully qualified ResourceObject
        return $typeName === 'ResourceObject'
            || $typeName === 'BEAR\Resource\ResourceObject'
            || $typeName === 'self';
    }
}
