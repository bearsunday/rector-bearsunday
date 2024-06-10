<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use TypeError;

use function array_filter;
use function array_values;

/** @see \Utils\Rector\Tests\Rector\RayQueryModuleRector\RayQueryModuleRectorTest */
final class RayQueryModuleRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change #[Named] to #[Sql] in Ray.QueryModule', [
            new CodeSample(
                <<<'CODE_SAMPLE'
#[Named('add_todo_item')] callable $todo
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
#[\Ray\Query\Annotation\Sql('todo_item_by_id')] \Ray\Query\InvokeInterface $todo
CODE_SAMPLE
            ),
        ]);
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /** @param Class_ $node */
    public function refactor(Node $node): ?Node
    {
        $params = $node->params;
        foreach ($params as $param) {
            $this->rectorParam($param, $node);
        }

        return $node;
    }

    function rectorParam(Node\Param $param, Node|Class_ $class): void
    {
        // Check if the parameter type is RowInterface
        if (
            $param->type instanceof Node\Name\FullyQualified
            && (! $param->type instanceof Node\UnionType)
            && ($param->type->toString() === 'Ray\Query\RowInterface' || $param->type->toString() === 'Ray\Query\RowListInterface')
        ) {
            $this->changeSqlAttr($param, $class);

            return;
        }

        if ($param->type->name === 'callable') {
            $this->changeSqlAttr($param, $class);
            $param->type = new Node\Name('\Ray\Query\InvokeInterface');
        }
    }

    public function changeSqlAttr(Node\Param $param, Node|Class_ $class): void
    {
        // Check if the parameter has Named attribute
        foreach ($param->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === 'Ray\Di\Di\Named') {
                    // Change the attribute name to Sql
                    $attr->name = new Node\Name('\Ray\Query\Annotation\Sql');

                    return;
                }
            }
        }

        $this->MethodNamedAttr($param, $class);
    }

    public function MethodNamedAttr(Node\Param $param, Node|Class_ $node): void
    {
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === 'Ray\Di\Di\Named') {
                    $named = $attr->args[0]->value->value;
                    // Create a Sql attribute
                    $attribute = new Node\Attribute(
                        new Node\Name('\Ray\Query\Annotation\Sql'),
                        [new Node\Arg(new Node\Scalar\String_($named))]
                    );

                    // Add the attribute to the parameter
                    $param->attrGroups[] = new Node\AttributeGroup([$attribute]);

                    // Remove the Named attribute from the class
                    $attrGroup->attrs = array_values(array_filter($attrGroup->attrs, static function ($attr) {
                        return $attr->name->toString() !== 'Ray\Di\Di\Named';
                    }));

                    // If the attrs array is empty, remove the attrGroup
                    if (empty($attrGroup->attrs)) {
                        $node->attrGroups = [];
                    }

                    return;
                }
            }
        }
    }
}
