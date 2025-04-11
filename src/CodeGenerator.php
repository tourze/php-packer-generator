<?php

namespace PhpPacker\Generator;

use PhpPacker\Ast\AstManagerInterface;
use PhpPacker\Generator\Config\GeneratorConfig;
use PhpPacker\Generator\Formatter\CodeFormatterInterface;
use PhpPacker\Generator\Formatter\DefaultFormatter;
use PhpPacker\Generator\Optimizer\CodeOptimizerInterface;
use PhpPacker\Generator\Optimizer\DefaultOptimizer;
use PhpPacker\Generator\Resource\ResourceHolderGenerator;
use PhpPacker\Generator\Visitor\RemoveNamespaceVisitor;
use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use Psr\Log\LoggerInterface;

class CodeGenerator implements CodeGeneratorInterface
{
    private GeneratorConfig $config;
    private AstManagerInterface $astManager;
    private LoggerInterface $logger;
    private Standard $printer;
    private CodeOptimizerInterface $optimizer;
    private CodeFormatterInterface $formatter;
    private ResourceHolderGenerator $resourceGenerator;

    public function __construct(
        GeneratorConfig $config,
        AstManagerInterface $astManager,
        LoggerInterface $logger,
        ?CodeOptimizerInterface $optimizer = null,
        ?CodeFormatterInterface $formatter = null
    ) {
        $this->config = $config;
        $this->astManager = $astManager;
        $this->logger = $logger;
        $this->printer = new Standard();
        $this->optimizer = $optimizer ?? new DefaultOptimizer($config, $logger);
        $this->formatter = $formatter ?? new DefaultFormatter($config, $logger);
        $this->resourceGenerator = new ResourceHolderGenerator($logger);
    }

    /**
     * @inheritDoc
     */
    public function generate(AstManagerInterface $astManager, array $phpFiles, array $resourceFiles): string
    {
        $this->logger->debug('开始生成代码');

        $statements = $this->mergeCode($astManager, $phpFiles, $resourceFiles);

        // 应用代码优化
        $statements = $this->optimizer->optimize($statements);

        // 对于需要使用kphp编译的项目，我们需要去除命名空间
        if ($this->config->shouldRemoveNamespace()) {
            $statements = $this->removeNamespaces($statements);
        }

        // 使用AST打印器生成格式化的代码
        $this->logger->debug('生成格式化的代码');
        $code = $this->printer->prettyPrintFile($statements);

        // 应用自定义格式化
        $code = $this->formatter->format($code);

        $this->logger->debug('代码生成完成');
        return $code;
    }

    /**
     * 合并代码
     */
    private function mergeCode(AstManagerInterface $astManager, array $phpFiles, array $resourceFiles): array
    {
        $statements = [];

        // 处理资源文件，增加释放逻辑，放到开头
        $resStatements = [];
        foreach ($resourceFiles as $resFile) {
            foreach ($this->resourceGenerator->generateResourceHolder($resFile) as $statement) {
                $resStatements[] = $statement;
            }
        }

        if (!empty($resStatements)) {
            $statements[] = new Node\Stmt\Namespace_(null, $resStatements);
        }

        // 添加PHP文件代码
        foreach ($phpFiles as $file) {
            $this->logger->debug('添加PHP文件代码: ' . $file);
            $statements = array_merge($statements, $astManager->getAst($file));
        }

        return $statements;
    }

    /**
     * 去除命名空间
     */
    private function removeNamespaces(array $statements): array
    {
        $this->logger->debug('移除命名空间');

        $traverser = $this->astManager->createNodeTraverser();
        $traverser->addVisitor(new RemoveNamespaceVisitor());
        $statements = $traverser->traverse($statements);

        return $statements;
    }
}
