<?php

namespace PhpPacker\Generator\Tests;

use PhpPacker\Ast\AstManagerInterface;
use PhpPacker\Generator\CodeGenerator;
use PhpPacker\Generator\Config\GeneratorConfig;
use PhpPacker\Generator\Formatter\CodeFormatterInterface;
use PhpPacker\Generator\Optimizer\CodeOptimizerInterface;
use PhpPacker\Generator\Resource\ResourceHolderGenerator;
use PhpParser\Node;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CodeGeneratorTest extends TestCase
{
    /** @var GeneratorConfig */
    private $config;

    /** @var AstManagerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $astManager;

    /** @var LoggerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var CodeOptimizerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $optimizer;

    /** @var CodeFormatterInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $formatter;

    /** @var CodeGenerator */
    private $generator;

    protected function setUp(): void
    {
        $this->config = new GeneratorConfig();
        $this->astManager = $this->createMock(AstManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->optimizer = $this->createMock(CodeOptimizerInterface::class);
        $this->formatter = $this->createMock(CodeFormatterInterface::class);

        $this->generator = new CodeGenerator(
            $this->config,
            $this->astManager,
            $this->logger,
            $this->optimizer,
            $this->formatter
        );
    }

    public function testGenerateWithNoFiles(): void
    {
        $phpFiles = [];
        $resourceFiles = [];
        $expectedOutput = "<?php\n\n"; // 空文件时会有一个PHP头部

        // 设置优化器和格式化器的行为
        $this->optimizer->expects($this->once())
            ->method('optimize')
            ->with([])
            ->willReturn([]);

        $this->formatter->expects($this->once())
            ->method('format')
            ->with($expectedOutput)
            ->willReturn($expectedOutput);

        // 调用生成方法
        $result = $this->generator->generate($this->astManager, $phpFiles, $resourceFiles);

        // 验证结果
        $this->assertEquals($expectedOutput, $result);
    }

    public function testGenerateWithPhpFiles(): void
    {
        $phpFiles = ['file1.php', 'file2.php'];
        $resourceFiles = [];
        $ast1 = [new Node\Stmt\Nop()];
        $ast2 = [new Node\Stmt\Echo_([new Node\Scalar\String_('test')])];
        $optimizedNodes = array_merge($ast1, $ast2);
        $expectedCode = "<?php\n\n// generated code";
        $formattedCode = "<?php\n// formatted code";

        // 设置AST管理器行为
        $this->astManager->expects($this->exactly(2))
            ->method('getAst')
            ->willReturnMap([
                ['file1.php', $ast1],
                ['file2.php', $ast2]
            ]);

        // 设置优化器行为
        $this->optimizer->expects($this->once())
            ->method('optimize')
            ->with($this->isType('array'))
            ->willReturn($optimizedNodes);

        // 设置格式化器行为
        $this->formatter->expects($this->once())
            ->method('format')
            ->with($this->isType('string'))
            ->willReturn($formattedCode);

        // 调用生成方法
        $result = $this->generator->generate($this->astManager, $phpFiles, $resourceFiles);

        // 验证结果
        $this->assertEquals($formattedCode, $result);
    }

    public function testGenerateWithNamespaceRemoval(): void
    {
        $phpFiles = ['file.php'];
        $resourceFiles = [];
        $ast = [new Node\Stmt\Namespace_(new Node\Name('Test'), [
            new Node\Stmt\Class_(new Node\Identifier('TestClass'))
        ])];
        $optimizedNodes = $ast;
        $traverser = $this->createMock(NodeTraverser::class);

        // 启用命名空间移除
        $this->config->setRemoveNamespace(true);

        // 设置AST管理器行为
        $this->astManager->expects($this->once())
            ->method('getAst')
            ->with('file.php')
            ->willReturn($ast);

        // 设置NodeTraverser的创建行为
        $this->astManager->expects($this->once())
            ->method('createNodeTraverser')
            ->willReturn($traverser);

        // 设置NodeTraverser的行为
        $traverser->expects($this->once())
            ->method('addVisitor')
            ->with($this->isInstanceOf(\PhpPacker\Generator\Visitor\RemoveNamespaceVisitor::class));

        $traverser->expects($this->once())
            ->method('traverse')
            ->with($optimizedNodes)
            ->willReturn([new Node\Stmt\Class_(new Node\Identifier('Test_TestClass'))]);

        // 设置优化器行为
        $this->optimizer->expects($this->once())
            ->method('optimize')
            ->willReturn($optimizedNodes);

        // 设置格式化器行为
        $this->formatter->expects($this->once())
            ->method('format')
            ->willReturn('<?php class Test_TestClass {}');

        // 调用生成方法
        $result = $this->generator->generate($this->astManager, $phpFiles, $resourceFiles);

        // 验证结果
        $this->assertEquals('<?php class Test_TestClass {}', $result);
    }

    public function testGenerateWithResourceFiles(): void
    {
        $phpFiles = [];
        $resourceFiles = ['resource.txt'];
        $resourceStatements = [new Expression(new Node\Expr\Variable('fileName'))];
        $expectedCode = "<?php\n\n// resource holder code";
        $formattedCode = "<?php\n// formatted resource code";

        // 创建ResourceHolderGenerator的模拟
        $resourceGenerator = $this->getMockBuilder(ResourceHolderGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        // 使用反射设置私有属性
        $reflection = new \ReflectionClass(CodeGenerator::class);
        $property = $reflection->getProperty('resourceGenerator');
        $property->setAccessible(true);
        $property->setValue($this->generator, $resourceGenerator);

        // 设置ResourceHolderGenerator的行为
        $resourceGenerator->expects($this->once())
            ->method('generateResourceHolder')
            ->with('resource.txt')
            ->willReturn(new \ArrayIterator($resourceStatements));

        // 设置优化器行为
        $this->optimizer->expects($this->once())
            ->method('optimize')
            ->willReturnCallback(function ($nodes) {
                // 验证节点中包含Namespace_节点
                $this->assertInstanceOf(Node\Stmt\Namespace_::class, $nodes[0]);
                return $nodes;
            });

        // 设置格式化器行为
        $this->formatter->expects($this->once())
            ->method('format')
            ->willReturn($formattedCode);

        // 调用生成方法
        $result = $this->generator->generate($this->astManager, $phpFiles, $resourceFiles);

        // 验证结果
        $this->assertEquals($formattedCode, $result);
    }
}
