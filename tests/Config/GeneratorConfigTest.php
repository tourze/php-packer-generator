<?php

namespace PhpPacker\Generator\Tests\Config;

use PhpPacker\Generator\Config\GeneratorConfig;
use PHPUnit\Framework\TestCase;

class GeneratorConfigTest extends TestCase
{
    private GeneratorConfig $config;

    protected function setUp(): void
    {
        $this->config = new GeneratorConfig();
    }

    public function testDefaultValues(): void
    {
        $this->assertTrue($this->config->shouldPreserveComments());
        $this->assertFalse($this->config->shouldRemoveNamespace());
        $this->assertTrue($this->config->shouldOptimizeCode());
        $this->assertNull($this->config->getLineEnding());
        $this->assertEquals(4, $this->config->getIndentationSize());
        $this->assertEquals(' ', $this->config->getIndentationChar());
    }

    public function testPreserveComments(): void
    {
        $this->assertTrue($this->config->shouldPreserveComments());

        $this->config->setPreserveComments(false);
        $this->assertFalse($this->config->shouldPreserveComments());

        $this->config->setPreserveComments(true);
        $this->assertTrue($this->config->shouldPreserveComments());
    }

    public function testRemoveNamespace(): void
    {
        $this->assertFalse($this->config->shouldRemoveNamespace());

        $this->config->setRemoveNamespace(true);
        $this->assertTrue($this->config->shouldRemoveNamespace());

        $this->config->setRemoveNamespace(false);
        $this->assertFalse($this->config->shouldRemoveNamespace());
    }

    public function testOptimizeCode(): void
    {
        $this->assertTrue($this->config->shouldOptimizeCode());

        $this->config->setOptimizeCode(false);
        $this->assertFalse($this->config->shouldOptimizeCode());

        $this->config->setOptimizeCode(true);
        $this->assertTrue($this->config->shouldOptimizeCode());
    }

    public function testLineEnding(): void
    {
        $this->assertNull($this->config->getLineEnding());

        $lineEnding = "\n";
        $this->config->setLineEnding($lineEnding);
        $this->assertEquals($lineEnding, $this->config->getLineEnding());

        $this->config->setLineEnding(null);
        $this->assertNull($this->config->getLineEnding());
    }

    public function testIndentationSize(): void
    {
        $this->assertEquals(4, $this->config->getIndentationSize());

        $this->config->setIndentationSize(2);
        $this->assertEquals(2, $this->config->getIndentationSize());
    }

    public function testIndentationChar(): void
    {
        $this->assertEquals(' ', $this->config->getIndentationChar());

        $this->config->setIndentationChar("\t");
        $this->assertEquals("\t", $this->config->getIndentationChar());
    }

    public function testFluentInterface(): void
    {
        $result = $this->config
            ->setPreserveComments(false)
            ->setRemoveNamespace(true)
            ->setOptimizeCode(false)
            ->setLineEnding("\r\n")
            ->setIndentationSize(2)
            ->setIndentationChar("\t");

        $this->assertSame($this->config, $result);
        $this->assertFalse($this->config->shouldPreserveComments());
        $this->assertTrue($this->config->shouldRemoveNamespace());
        $this->assertFalse($this->config->shouldOptimizeCode());
        $this->assertEquals("\r\n", $this->config->getLineEnding());
        $this->assertEquals(2, $this->config->getIndentationSize());
        $this->assertEquals("\t", $this->config->getIndentationChar());
    }
}
