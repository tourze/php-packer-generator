<?php

namespace PhpPacker\Generator\Tests\Optimizer;

use PhpPacker\Generator\Config\GeneratorConfig;
use PhpPacker\Generator\Optimizer\DefaultOptimizer;
use PhpParser\Comment;
use PhpParser\Node\Expr\Variable;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DefaultOptimizerTest extends TestCase
{
    /** @var GeneratorConfig */
    private $config;

    /** @var LoggerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var DefaultOptimizer */
    private $optimizer;

    protected function setUp(): void
    {
        $this->config = new GeneratorConfig();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->optimizer = new DefaultOptimizer($this->config, $this->logger);
    }

    public function testOptimizeWithDefaultConfig(): void
    {
        // 创建带注释的节点
        $var = new Variable('test');
        $var->setAttribute('comments', [new Comment('// This is a test comment')]);
        $nodes = [$var];

        $this->logger->expects($this->once())
            ->method('debug')
            ->with($this->equalTo('优化代码'));

        // 默认配置下保留注释
        $result = $this->optimizer->optimize($nodes);

        $this->assertCount(1, $result);
        $this->assertSame($var->getAttribute('comments'), $result[0]->getAttribute('comments'));
    }

    public function testOptimizeWithoutComments(): void
    {
        // 创建带注释的节点
        $var = new Variable('test');
        $var->setAttribute('comments', [new Comment('// This is a test comment')]);
        $nodes = [$var];

        // 设置不保留注释
        $this->config->setPreserveComments(false);

        // 在PHPUnit 10中不再支持withConsecutive，需要分别设置各个方法调用期望
        $this->logger->expects($this->exactly(2))
            ->method('debug')
            ->willReturnCallback(function ($message) {
                static $callCount = 0;
                $callCount++;

                if ($callCount === 1) {
                    $this->assertEquals('优化代码', $message);
                } elseif ($callCount === 2) {
                    $this->assertEquals('移除代码注释', $message);
                }

                return null;
            });

        $result = $this->optimizer->optimize($nodes);

        $this->assertCount(1, $result);
        $this->assertEmpty($result[0]->getAttribute('comments'));
    }

    public function testOptimizeWithoutOptimization(): void
    {
        // 创建测试节点
        $var = new Variable('test');
        $nodes = [$var];

        // 禁用代码优化
        $this->config->setOptimizeCode(false);

        // 在PHPUnit 10中不再支持withConsecutive，需要分别设置各个方法调用期望
        $this->logger->expects($this->exactly(2))
            ->method('debug')
            ->willReturnCallback(function ($message) {
                static $callCount = 0;
                $callCount++;

                if ($callCount === 1) {
                    $this->assertEquals('优化代码', $message);
                } elseif ($callCount === 2) {
                    $this->assertEquals('代码优化已禁用，跳过优化', $message);
                }

                return null;
            });

        $result = $this->optimizer->optimize($nodes);

        // 禁用优化后，节点应该原样返回
        $this->assertSame($nodes, $result);
    }
}
