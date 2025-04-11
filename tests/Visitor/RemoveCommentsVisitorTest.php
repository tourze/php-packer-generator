<?php

namespace PhpPacker\Generator\Tests\Visitor;

use PhpPacker\Generator\Visitor\RemoveCommentsVisitor;
use PhpParser\Comment;
use PhpParser\Node\Expr\Variable;
use PHPUnit\Framework\TestCase;

class RemoveCommentsVisitorTest extends TestCase
{
    private RemoveCommentsVisitor $visitor;

    protected function setUp(): void
    {
        $this->visitor = new RemoveCommentsVisitor();
    }

    public function testRemoveComments(): void
    {
        // 创建一个带注释的节点
        $comments = [
            new Comment('// This is a test comment'),
            new Comment('/* This is a block comment */')
        ];

        $node = new Variable('test');
        $node->setAttribute('comments', $comments);

        // 应用访问者
        $result = $this->visitor->leaveNode($node);

        // 验证注释被移除
        $this->assertEmpty($result->getAttribute('comments'));
    }

    public function testNodeWithoutComments(): void
    {
        // 创建一个没有注释的节点
        $node = new Variable('test');

        // 应用访问者
        $result = $this->visitor->leaveNode($node);

        // 节点应该原样返回，没有注释属性
        $this->assertEmpty($result->getAttribute('comments', []));
    }

    public function testReturnsSameNode(): void
    {
        // 创建测试节点
        $node = new Variable('test');

        // 应用访问者
        $result = $this->visitor->leaveNode($node);

        // 返回的应该是同一个节点实例
        $this->assertSame($node, $result);
    }
}
