# PHP-Packer-Generator

[![Latest Version](https://img.shields.io/packagist/v/tourze/php-packer-generator.svg)](https://packagist.org/packages/tourze/php-packer-generator)
[![Build Status](https://github.com/tourze/php-packer-generator/workflows/CI/badge.svg)](https://github.com/tourze/php-packer-generator/actions)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

The **PHP-Packer-Generator** is a code generation component for the PHP-Packer project, responsible for merging, optimizing, and formatting PHP code.

## Features

- **Code Merging**: Merge multiple PHP files into a single file.
- **Code Optimization**: Support for code minification, comment preservation, and more.
- **Output Formatting**: Control the formatting and style of the generated code.
- **Resource Embedding**: Generate code to embed resource files.
- **Highly Extensible**: Support custom code optimizers and formatters.

## Installation

Requirements:

- PHP >= 8.1
- Composer

Install via Composer:

```bash
composer require tourze/php-packer-generator
```

## Quick Start

Basic usage example:

```php
use PhpPacker\Generator\CodeGenerator;
use PhpPacker\Generator\Config\GeneratorConfig;
use PhpPacker\Ast\AstManager;
use Psr\Log\LoggerInterface;

// Create config
$config = new GeneratorConfig();
$config->setPreserveComments(true);
$config->setRemoveNamespace(false);

// Create code generator
$astManager = new AstManager($logger); // $logger must implement LoggerInterface
$generator = new CodeGenerator($config, $astManager, $logger);

// Generate code
$code = $generator->generate($astManager, $phpFiles, $resourceFiles);
```

## Custom Code Generation

You can extend the code generation process by implementing your own `CodeOptimizer` and `CodeFormatter`:

```php
use PhpPacker\Generator\Optimizer\CustomOptimizer;
use PhpPacker\Generator\Formatter\CustomFormatter;

$optimizer = new CustomOptimizer();
$formatter = new CustomFormatter();

$generator = new CodeGenerator($config, $astManager, $logger, $optimizer, $formatter);
```

## Configuration

The `GeneratorConfig` class provides options to control code generation:

- `setPreserveComments(bool $preserve)`: Whether to keep comments.
- `setRemoveNamespace(bool $remove)`: Whether to remove namespaces (useful for KPHP compatibility).
- `setOptimizeCode(bool $optimize)`: Enable/disable code optimization.
- `setLineEnding(?string $ending)`: Set custom line ending (e.g., `"\r\n"`).
- `setIndentationSize(int $size)`: Set indentation size.
- `setIndentationChar(string $char)`: Set indentation character (e.g., space or tab).

## Advanced Usage

- **Resource Embedding**: Use `ResourceHolderGenerator` to generate code for resource files.
- **Custom Visitors**: Implement your own AST visitors for advanced code transformations.

## Contribution Guide

- Please submit issues and pull requests via GitHub.
- Follow PSR code style guidelines.
- Ensure all tests pass (`phpunit`).
- Add tests for new features.

## License

MIT License. See [LICENSE](LICENSE) for details.

## Changelog

See [Releases](https://github.com/tourze/php-packer-generator/releases) for version history and upgrade notes.
