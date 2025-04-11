<?php

namespace PhpPacker\Generator\Tests\Resource;

use PhpPacker\Generator\Exception\GeneratorException;
use PhpPacker\Generator\Resource\ResourceHolderGenerator;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ResourceHolderGeneratorTest extends TestCase
{
    /** @var ResourceHolderGenerator */
    private $resourceGenerator;

    /** @var LoggerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->resourceGenerator = new ResourceHolderGenerator($this->logger);
    }

    public function testGenerateResourceHolder(): void
    {
        // 创建一个临时文件作为资源文件
        $tempFile = tempnam(sys_get_temp_dir(), 'res');
        file_put_contents($tempFile, 'Test content');

        // 配置日志记录器期望
        $this->logger->expects($this->once())
            ->method('debug')
            ->with($this->stringContains('Generating resource holder for'));

        try {
            // 调用被测试的方法
            $result = iterator_to_array($this->resourceGenerator->generateResourceHolder($tempFile));

            // 验证结果
            $this->assertCount(3, $result, '应该生成3个节点');
            $this->assertInstanceOf(Expression::class, $result[0], '第一个节点应该是赋值表达式');
            $this->assertInstanceOf(If_::class, $result[1], '第二个节点应该是条件语句');
            $this->assertInstanceOf(If_::class, $result[2], '第三个节点应该是条件语句');
        } finally {
            // 清理
            @unlink($tempFile);
        }
    }

    public function testGenerateResourceHolderWithNonExistingFile(): void
    {
        // 配置日志记录器期望
        $this->logger->expects($this->once())
            ->method('debug')
            ->with($this->stringContains('Generating resource holder for'));

        // 使用不存在的文件路径会导致异常
        $this->expectException(GeneratorException::class);
        $this->expectExceptionMessage('Resource file not found:');

        // 调用方法
        iterator_to_array($this->resourceGenerator->generateResourceHolder('/non/existent/file.txt'));
    }
}
