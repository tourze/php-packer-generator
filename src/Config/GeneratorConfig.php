<?php

namespace PhpPacker\Generator\Config;

class GeneratorConfig
{
    private bool $preserveComments = true;
    private bool $removeNamespace = false;
    private bool $optimizeCode = true;
    private ?string $lineEnding = null;
    private int $indentationSize = 4;
    private string $indentationChar = ' ';

    /**
     * 是否保留注释
     */
    public function shouldPreserveComments(): bool
    {
        return $this->preserveComments;
    }

    /**
     * 设置是否保留注释
     */
    public function setPreserveComments(bool $preserve): self
    {
        $this->preserveComments = $preserve;
        return $this;
    }

    /**
     * 是否移除命名空间
     */
    public function shouldRemoveNamespace(): bool
    {
        return $this->removeNamespace;
    }

    /**
     * 设置是否移除命名空间
     */
    public function setRemoveNamespace(bool $remove): self
    {
        $this->removeNamespace = $remove;
        return $this;
    }

    /**
     * 是否优化代码
     */
    public function shouldOptimizeCode(): bool
    {
        return $this->optimizeCode;
    }

    /**
     * 设置是否优化代码
     */
    public function setOptimizeCode(bool $optimize): self
    {
        $this->optimizeCode = $optimize;
        return $this;
    }

    /**
     * 获取行结束符
     */
    public function getLineEnding(): ?string
    {
        return $this->lineEnding;
    }

    /**
     * 设置行结束符
     */
    public function setLineEnding(?string $lineEnding): self
    {
        $this->lineEnding = $lineEnding;
        return $this;
    }

    /**
     * 获取缩进大小
     */
    public function getIndentationSize(): int
    {
        return $this->indentationSize;
    }

    /**
     * 设置缩进大小
     */
    public function setIndentationSize(int $size): self
    {
        $this->indentationSize = $size;
        return $this;
    }

    /**
     * 获取缩进字符
     */
    public function getIndentationChar(): string
    {
        return $this->indentationChar;
    }

    /**
     * 设置缩进字符
     */
    public function setIndentationChar(string $char): self
    {
        $this->indentationChar = $char;
        return $this;
    }
}
