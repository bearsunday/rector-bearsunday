<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/** @see \Utils\Rector\Tests\Rector\RayQueryModuleRector\RayQueryModuleRectorTest */
final class RayQueryModuleRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('// @todo fill the description', [
            new CodeSample(
                <<<'CODE_SAMPLE'
// @todo fill code before
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// @todo fill code after
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
            $this->rectorParam($param);
        }

        return $node;
    }

    function rectorParam(Node\Param $param): void
    {
        // Check if the parameter type is RowInterface
        if (
            $param->type instanceof Node\Name\FullyQualified
            && $param->type->toString() === 'Ray\Query\RowInterface'
            || $param->type->toString() === 'Ray\Query\RowListInterface'
        ) {
            // Check if the parameter has Named attribute
            $this->changeSqlAttr($param);
        }

        if ($param->type->name === 'callable') {
            $this->changeSqlAttr($param);
            $param->type = new Node\Name('\Ray\Query\InvokeInterface');
        }
    }

    public function changeSqlAttr(Node\Param $param): void
    {
        foreach ($param->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                if ($attr->name->toString() === 'Ray\Di\Di\Named') {
                    // Change the attribute name to Sql
                    $attr->name = new Node\Name('\Ray\Query\Annotation\Sql');

                    return;
                }
            }
        }
    }
}
