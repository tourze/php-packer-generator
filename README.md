# PHP-Packer-Generator

这个包是PHP-Packer项目的代码生成组件，负责代码的合并、优化和格式控制。

## 主要功能

- **代码合并**: 将多个PHP文件合并成一个单一文件
- **代码优化**: 支持代码精简、注释保留等优化功能
- **输出格式控制**: 控制生成代码的格式和风格

## 安装

```bash
composer require tourze/php-packer-generator
```

## 使用方法

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
$astManager = new AstManager($logger);
$generator = new CodeGenerator($config, $astManager, $logger);

// 生成代码
$code = $generator->generate($astManager, $phpFiles, $resourceFiles);
```

## 自定义代码生成

通过实现自定义的`CodeOptimizer`和`CodeFormatter`，可以扩展代码生成的行为:

```php
use PhpPacker\Generator\Optimizer\CustomOptimizer;
use PhpPacker\Generator\Formatter\CustomFormatter;

$optimizer = new CustomOptimizer();
$formatter = new CustomFormatter();

$generator = new CodeGenerator($config, $astManager, $logger, $optimizer, $formatter);
```
