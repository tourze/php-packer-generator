<?php

namespace PhpPacker\Generator\Formatter;

interface CodeFormatterInterface
{
    /**
     * 格式化生成的代码
     *
     * @param string $code 原始生成的代码
     * @return string 格式化后的代码
     */
    public function format(string $code): string;
}
