<?php

namespace PhpPacker\Generator\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class RemoveCommentsVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node)
    {
        // 清除节点的所有注释
        $node->setAttribute('comments', []);
        return $node;
    }
}
