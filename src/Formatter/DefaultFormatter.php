<?php

namespace PhpPacker\Generator\Formatter;

use PhpPacker\Generator\Config\GeneratorConfig;
use Psr\Log\LoggerInterface;

class DefaultFormatter implements CodeFormatterInterface
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
    public function format(string $code): string
    {
        $this->logger->debug('格式化生成的代码');

        // 处理行结束符
        if ($this->config->getLineEnding() !== null) {
            // 先统一替换为单一字符，再替换为目标行结束符
            // 先处理 \r\n 防止拆分
            $code = str_replace("\r\n", "\n", $code);
            // 再处理其它换行符
            $code = str_replace("\r", "\n", $code);
            // 最后转换为目标换行符
            $code = str_replace("\n", $this->config->getLineEnding(), $code);
        }

        // 这里可以添加更多的格式化逻辑

        return $code;
    }
}
