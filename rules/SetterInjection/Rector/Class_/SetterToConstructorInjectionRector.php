<?php

declare(strict_types=1);

namespace Rector\BearSunday\SetterInjection\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Ray\Di\Di\Inject;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function array_filter;
use function array_values;
use function assert;
use function count;
use function lcfirst;
use function str_starts_with;
use function substr;

/**
 * Converts setter injection with #[Inject] to constructor injection
 *
 * @see \Rector\Tests\SetterInjection\Rector\Class_\SetterToConstructorInjectionRector\SetterToConstructorInjectionRectorTest
 */
final class SetterToConstructorInjectionRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert setter injection with #[Inject] to constructor injection', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private FooInterface $foo;

    #[Inject]
    public function setFoo(FooInterface $foo): void
    {
        $this->foo = $foo;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function __construct(
        private readonly FooInterface $foo
    ) {
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

        $injectSetters = $this->findInjectSetters($node);
        if ($injectSetters === []) {
            return null;
        }

        $propertiesToRemove = [];
        $newConstructorParams = [];

        foreach ($injectSetters as $setter) {
            $setterInfo = $this->extractSetterInfo($setter);
            if ($setterInfo === null) {
                continue;
            }

            [$paramType, $paramName, $propertyName] = $setterInfo;

            // Create constructor parameter with promoted property
            $param = new Param(
                new Variable($paramName),
                null,
                $paramType,
                false,
                false,
                [],
                Class_::MODIFIER_PRIVATE | Class_::MODIFIER_READONLY
            );

            $newConstructorParams[] = $param;
            $propertiesToRemove[] = $propertyName;
        }

        if ($newConstructorParams === []) {
            return null;
        }

        // Find or create constructor
        $constructor = $node->getMethod('__construct');
        if ($constructor === null) {
            $constructor = new ClassMethod(new Identifier('__construct'));
            $constructor->flags = Class_::MODIFIER_PUBLIC;
            $constructor->params = [];
            $constructor->stmts = [];

            // Add constructor at the beginning of the class
            array_unshift($node->stmts, $constructor);
        }

        // Add new parameters to constructor
        $constructor->params = array_merge($constructor->params, $newConstructorParams);

        // Remove setter methods and related properties
        $node->stmts = array_values(array_filter($node->stmts, function ($stmt) use ($injectSetters, $propertiesToRemove) {
            // Remove setter methods
            if ($stmt instanceof ClassMethod) {
                foreach ($injectSetters as $setter) {
                    if ($stmt === $setter) {
                        return false;
                    }
                }
            }

            // Remove properties that were converted
            if ($stmt instanceof Property) {
                foreach ($stmt->props as $prop) {
                    if (in_array($prop->name->toString(), $propertiesToRemove, true)) {
                        return false;
                    }
                }
            }

            return true;
        }));

        return $node;
    }

    /**
     * @return ClassMethod[]
     */
    private function findInjectSetters(Class_ $class): array
    {
        $setters = [];

        foreach ($class->getMethods() as $method) {
            if (! str_starts_with($method->name->toString(), 'set')) {
                continue;
            }

            foreach ($method->attrGroups as $attrGroup) {
                foreach ($attrGroup->attrs as $attr) {
                    $attrName = $attr->name->toString();
                    if ($attrName === 'Inject' || $attrName === 'Ray\Di\Di\Inject') {
                        $setters[] = $method;
                        break 2;
                    }
                }
            }
        }

        return $setters;
    }

    /**
     * @return array{Node\Name|Node\Identifier|Node\ComplexType|null, string, string}|null
     */
    private function extractSetterInfo(ClassMethod $method): ?array
    {
        if (count($method->params) !== 1) {
            return null;
        }

        $param = $method->params[0];
        $paramType = $param->type;
        $paramName = $param->var->name;

        // Try to find property name from assignment in method body
        $propertyName = $paramName;
        if ($method->stmts !== null) {
            foreach ($method->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\Expression && $stmt->expr instanceof Assign) {
                    $assign = $stmt->expr;
                    if ($assign->var instanceof PropertyFetch && $assign->var->var instanceof Variable) {
                        if ($assign->var->var->name === 'this' && $assign->var->name instanceof Identifier) {
                            $propertyName = $assign->var->name->toString();
                            break;
                        }
                    }
                }
            }
        }

        return [$paramType, $paramName, $propertyName];
    }
}
