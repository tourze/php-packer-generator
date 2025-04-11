<?php

namespace PhpPacker\Generator\Optimizer;

use PhpParser\Node;

interface CodeOptimizerInterface
{
    /**
     * 优化AST节点
     *
     * @param array<Node> $nodes 要优化的AST节点
     * @return array<Node> 优化后的AST节点
     */
    public function optimize(array $nodes): array;
}
