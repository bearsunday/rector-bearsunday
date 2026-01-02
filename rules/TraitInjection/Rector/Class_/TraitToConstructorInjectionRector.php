<?php

declare(strict_types=1);

namespace Rector\BearSunday\TraitInjection\Rector\Class_;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function array_filter;
use function array_merge;
use function array_values;
use function assert;

/**
 * Converts trait-based injection to constructor injection
 *
 * @see \Rector\Tests\TraitInjection\Rector\Class_\TraitToConstructorInjectionRector\TraitToConstructorInjectionRectorTest
 */
final class TraitToConstructorInjectionRector extends AbstractRector
{
    /**
     * Mapping of trait names to [interface, property name]
     * @var array<string, array{0: string, 1: string}>
     */
    private const TRAIT_TO_INJECTION = [
        'BEAR\Resource\ResourceInject' => ['BEAR\Resource\ResourceInterface', 'resource'],
        'BEAR\Sunday\Inject\ResourceInject' => ['BEAR\Resource\ResourceInterface', 'resource'],
        'Ray\Di\InjectorInject' => ['Ray\Di\InjectorInterface', 'injector'],
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert trait-based injection to constructor injection', [
            new CodeSample(
                <<<'CODE_SAMPLE'
use BEAR\Resource\ResourceInject;

class SomeClass
{
    use ResourceInject;

    public function doSomething(): void
    {
        $this->resource->get('app://self/user');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use BEAR\Resource\ResourceInterface;

class SomeClass
{
    public function __construct(
        private readonly ResourceInterface $resource
    ) {
    }

    public function doSomething(): void
    {
        $this->resource->get('app://self/user');
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
        return [Class_::class, Use_::class];
    }

    /**
     * @param Class_|Use_ $node
     * @return Node|Node[]|int|null
     */
    public function refactor(Node $node): Node|array|int|null
    {
        if ($node instanceof Use_) {
            return $this->refactorUse($node);
        }

        assert($node instanceof Class_);

        $traitsToRemove = [];
        $newConstructorParams = [];

        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $trait) {
                $traitName = $trait->toString();

                foreach (self::TRAIT_TO_INJECTION as $knownTrait => $injection) {
                    if ($this->matchesTraitName($traitName, $knownTrait)) {
                        [$interfaceName, $propertyName] = $injection;

                        $param = new Param(
                            new Variable($propertyName),
                            null,
                            new FullyQualified($interfaceName),
                            false,
                            false,
                            [],
                            Class_::MODIFIER_PRIVATE | Class_::MODIFIER_READONLY
                        );

                        $newConstructorParams[] = $param;
                        $traitsToRemove[] = $traitName;
                        break;
                    }
                }
            }
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

            // Find the position after trait uses to insert constructor
            $insertPosition = 0;
            foreach ($node->stmts as $index => $stmt) {
                if ($stmt instanceof TraitUse) {
                    $insertPosition = $index + 1;
                }
            }

            array_splice($node->stmts, $insertPosition, 0, [$constructor]);
        }

        // Add new parameters to constructor (at the end)
        $constructor->params = array_merge($constructor->params, $newConstructorParams);

        // Remove trait uses
        $node->stmts = array_values(array_filter($node->stmts, function ($stmt) use ($traitsToRemove) {
            if (! $stmt instanceof TraitUse) {
                return true;
            }

            // Filter out matching traits
            $remainingTraits = array_filter($stmt->traits, function ($trait) use ($traitsToRemove) {
                $traitName = $trait->toString();
                foreach ($traitsToRemove as $toRemove) {
                    if ($this->matchesTraitName($traitName, $toRemove)) {
                        return false;
                    }
                }
                return true;
            });

            // If no traits remain, remove the entire statement
            if ($remainingTraits === []) {
                return false;
            }

            // Update with remaining traits
            $stmt->traits = array_values($remainingTraits);
            return true;
        }));

        return $node;
    }

    private function matchesTraitName(string $usedTrait, string $knownTrait): bool
    {
        // Exact match
        if ($usedTrait === $knownTrait) {
            return true;
        }

        // Match short name (e.g., "ResourceInject" matches "BEAR\Resource\ResourceInject")
        $shortName = substr($knownTrait, strrpos($knownTrait, '\\') + 1);
        return $usedTrait === $shortName;
    }

    private function refactorUse(Use_ $node): int|null
    {
        foreach ($node->uses as $useUse) {
            $useName = $useUse->name->toString();
            if (isset(self::TRAIT_TO_INJECTION[$useName])) {
                return NodeVisitor::REMOVE_NODE;
            }
        }

        return null;
    }
}
