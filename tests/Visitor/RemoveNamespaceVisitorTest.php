<?php

namespace PhpPacker\Generator\Tests\Visitor;

use PhpPacker\Generator\Visitor\RemoveNamespaceVisitor;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;

class RemoveNamespaceVisitorTest extends TestCase
{
    private RemoveNamespaceVisitor $visitor;

    protected function setUp(): void
    {
        $this->visitor = new RemoveNamespaceVisitor();
    }

    public function testRemoveEmptyNamespace(): void
    {
        $node = new Node\Stmt\Namespace_(new Node\Name('Test'));

        $result = $this->visitor->leaveNode($node);

        $this->assertEquals(NodeTraverser::REMOVE_NODE, $result);
    }

    public function testRemoveNamespaceWithStatementsReturnsStatements(): void
    {
        $statements = [
            new Node\Stmt\Class_(new Node\Identifier('TestClass')),
            new Node\Stmt\Function_(new Node\Identifier('testFunction'))
        ];

        $node = new Node\Stmt\Namespace_(new Node\Name('Test'), $statements);

        $result = $this->visitor->leaveNode($node);

        $this->assertSame($statements, $result);
        $this->assertEquals('Test', $this->visitor->namespace);
    }

    public function testTransformClassNameWithNamespace(): void
    {
        // 先设置命名空间上下文
        $nsNode = new Node\Stmt\Namespace_(new Node\Name('Test'));
        $this->visitor->leaveNode($nsNode);

        // 创建一个有命名空间的类
        $classNode = new Node\Stmt\Class_(new Node\Identifier('TestClass'));
        $classNode->namespacedName = new Node\Name\FullyQualified('Test\TestClass');

        $result = $this->visitor->leaveNode($classNode);

        // 结果应该是类名被转换为Test_TestClass
        $this->assertEquals('Test_TestClass', $result->name->name);
    }

    public function testSkipClassWithoutNamespacedName(): void
    {
        $classNode = new Node\Stmt\Class_(new Node\Identifier('AnonymousClass'));
        $classNode->namespacedName = null;

        $result = $this->visitor->leaveNode($classNode);

        $this->assertNull($result);
    }

    public function testTransformFullyQualifiedName(): void
    {
        $node = new Node\Name\FullyQualified('Vendor\Package\Class');

        $result = $this->visitor->leaveNode($node);

        $this->assertInstanceOf(Node\Name::class, $result);

        // 直接检查getFormatName的结果
        $reflection = new \ReflectionClass(RemoveNamespaceVisitor::class);
        $method = $reflection->getMethod('getFormatName');
        $method->setAccessible(true);

        $expected = $method->invoke($this->visitor, 'Vendor\Package\Class');
        $this->assertEquals('Vendor_Package_Class', $expected);
    }

    public function testGetFormatName(): void
    {
        $reflection = new \ReflectionClass(RemoveNamespaceVisitor::class);
        $method = $reflection->getMethod('getFormatName');
        $method->setAccessible(true);

        $this->assertEquals('Test_Class', $method->invoke($this->visitor, 'Test\Class'));
        $this->assertEquals('Test_Class', $method->invoke($this->visitor, '\Test\Class'));
        $this->assertEquals('Single', $method->invoke($this->visitor, 'Single'));
    }
}
