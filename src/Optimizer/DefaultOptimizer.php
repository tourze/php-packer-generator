<?php

namespace PhpPacker\Generator\Optimizer;

use PhpPacker\Generator\Config\GeneratorConfig;
use PhpPacker\Generator\Visitor\RemoveCommentsVisitor;
use PhpParser\NodeTraverser;
use Psr\Log\LoggerInterface;

class DefaultOptimizer implements CodeOptimizerInterface
{
    private GeneratorConfig $config;
    private LoggerInterface $logger;

    public function __construct(GeneratorConfig $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function optimize(array $nodes): array
    {
        $this->logger->debug('优化代码');

        if (!$this->config->shouldOptimizeCode()) {
            $this->logger->debug('代码优化已禁用，跳过优化');
            return $nodes;
        }

        $traverser = new NodeTraverser();

        // 如果不保留注释，则移除所有注释
        if (!$this->config->shouldPreserveComments()) {
            $this->logger->debug('移除代码注释');
            $traverser->addVisitor(new RemoveCommentsVisitor());
        }

        // 可以在这里添加更多的优化访问者

        return $traverser->traverse($nodes);
    }
}
