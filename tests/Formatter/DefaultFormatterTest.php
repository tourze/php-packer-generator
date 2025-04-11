<?php

namespace PhpPacker\Generator\Tests\Formatter;

use PhpPacker\Generator\Config\GeneratorConfig;
use PhpPacker\Generator\Formatter\DefaultFormatter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DefaultFormatterTest extends TestCase
{
    /** @var GeneratorConfig */
    private $config;

    /** @var LoggerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var DefaultFormatter */
    private $formatter;

    protected function setUp(): void
    {
        $this->config = new GeneratorConfig();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->formatter = new DefaultFormatter($this->config, $this->logger);
    }

    public function testFormatWithDefaultConfig(): void
    {
        $code = "Line1\nLine2\nLine3";

        $this->logger->expects($this->once())
            ->method('debug')
            ->with('格式化生成的代码');

        $result = $this->formatter->format($code);

        // 默认配置下不改变行结束符
        $this->assertEquals($code, $result);
    }

    public function testFormatWithCustomLineEnding(): void
    {
        $originalCode = "Line1\nLine2\r\nLine3\rLine4";
        $expectedCode = "Line1\r\nLine2\r\nLine3\r\nLine4";

        $this->config->setLineEnding("\r\n");

        $result = $this->formatter->format($originalCode);

        $this->assertEquals($expectedCode, $result);
    }

    public function testFormatWithNullLineEnding(): void
    {
        $originalCode = "Line1\nLine2\nLine3";

        $this->config->setLineEnding(null);

        $result = $this->formatter->format($originalCode);

        // 行结束符为null时不做改变
        $this->assertEquals($originalCode, $result);
    }
}
