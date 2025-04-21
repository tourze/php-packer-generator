# PHP-Packer-Generator

[![最新版本](https://img.shields.io/packagist/v/tourze/php-packer-generator.svg)](https://packagist.org/packages/tourze/php-packer-generator)
[![构建状态](https://github.com/tourze/php-packer-generator/workflows/CI/badge.svg)](https://github.com/tourze/php-packer-generator/actions)
[![许可证](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

**PHP-Packer-Generator** 是 PHP-Packer 项目的代码生成组件，负责 PHP 代码的合并、优化与格式化输出。

## 功能特性

- **代码合并**：将多个 PHP 文件合并为单一文件
- **代码优化**：支持代码精简、注释保留等多种优化
- **输出格式控制**：可自定义生成代码的风格和格式
- **资源嵌入**：可生成嵌入资源文件的代码
- **高度可扩展**：支持自定义优化器与格式化器

## 安装说明

环境要求：

- PHP >= 8.1
- Composer

使用 Composer 安装：

```bash
composer require tourze/php-packer-generator
```

## 快速开始

基本用法示例：

```php
use PhpPacker\Generator\CodeGenerator;
use PhpPacker\Generator\Config\GeneratorConfig;
use PhpPacker\Ast\AstManager;
use Psr\Log\LoggerInterface;

// 创建配置
$config = new GeneratorConfig();
$config->setPreserveComments(true);
$config->setRemoveNamespace(false);

// 创建代码生成器
$astManager = new AstManager($logger); // $logger 需实现 LoggerInterface
$generator = new CodeGenerator($config, $astManager, $logger);

// 生成代码
$code = $generator->generate($astManager, $phpFiles, $resourceFiles);
```

## 自定义代码生成

你可以通过实现自定义的 `CodeOptimizer` 和 `CodeFormatter` 扩展代码生成流程：

```php
use PhpPacker\Generator\Optimizer\CustomOptimizer;
use PhpPacker\Generator\Formatter\CustomFormatter;

$optimizer = new CustomOptimizer();
$formatter = new CustomFormatter();

$generator = new CodeGenerator($config, $astManager, $logger, $optimizer, $formatter);
```

## 配置项说明

`GeneratorConfig` 配置类提供如下选项：

- `setPreserveComments(bool $preserve)`：是否保留注释
- `setRemoveNamespace(bool $remove)`：是否移除命名空间（适用于 KPHP 场景）
- `setOptimizeCode(bool $optimize)`：是否启用代码优化
- `setLineEnding(?string $ending)`：自定义换行符（如 `"\r\n"`）
- `setIndentationSize(int $size)`：缩进空格数
- `setIndentationChar(string $char)`：缩进字符（空格或制表符）

## 进阶用法

- **资源嵌入**：可通过 `ResourceHolderGenerator` 生成资源文件嵌入代码
- **自定义 Visitor**：可自定义 AST Visitor，实现高级代码转换

## 贡献指南

- 欢迎通过 GitHub 提交 Issue 和 PR
- 遵循 PSR 代码风格
- 所有代码需通过测试（`phpunit`）
- 新特性需补充测试用例

## 许可证

MIT 开源协议，详见 [LICENSE](LICENSE)

## 更新日志

请参见 [Releases](https://github.com/tourze/php-packer-generator/releases) 获取版本历史与升级说明。
