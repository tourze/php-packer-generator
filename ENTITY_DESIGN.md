# Entity Design: PHP-Packer-Generator

本模块本身不直接定义数据库实体（Entity），但其核心类结构可视为“逻辑实体”，包括：

- `CodeGenerator`：主入口，负责 orchestrate 代码生成流程
- `GeneratorConfig`：配置项实体，定义所有生成参数
- `DefaultOptimizer` / `CodeOptimizerInterface`：代码优化器实体
- `DefaultFormatter` / `CodeFormatterInterface`：代码格式化器实体
- `ResourceHolderGenerator`：资源文件嵌入实体
- `RemoveCommentsVisitor`、`RemoveNamespaceVisitor`：AST 访问器实体

## 设计说明

- **高内聚低耦合**：所有实体通过接口解耦，便于扩展和单元测试。
- **配置驱动**：所有生成行为均可通过 `GeneratorConfig` 灵活配置。
- **可插拔优化/格式化**：支持自定义实现优化器和格式化器，满足不同场景需求。
- **资源处理**：`ResourceHolderGenerator` 负责资源文件的安全嵌入和校验。

> 如需扩展实体，可参考现有接口进行实现。
