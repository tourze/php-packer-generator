<?php

namespace PhpPacker\Generator;

use PhpPacker\Ast\AstManagerInterface;

interface CodeGeneratorInterface
{
    /**
     * 生成代码
     *
     * @param AstManagerInterface $astManager AST管理器
     * @param array $phpFiles 要合并的PHP文件列表
     * @param array $resourceFiles 要包含的资源文件列表
     * @return string 生成的代码
     */
    public function generate(AstManagerInterface $astManager, array $phpFiles, array $resourceFiles): string;
}
