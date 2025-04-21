# PHP-Packer-Generator Workflow (Mermaid)

Below is the main workflow of the PHP-Packer-Generator module, illustrated in Mermaid:

```mermaid
flowchart TD
    A[Start] --> B[Initialize GeneratorConfig]
    B --> C[Create AstManager]
    C --> D[Instantiate CodeGenerator]
    D --> E[Call generate()]
    E --> F[Merge PHP and resource files]
    F --> G[Optimize AST nodes]
    G --> H{Remove Namespace?}
    H -- Yes --> I[Remove namespaces from AST]
    H -- No --> J[Skip namespace removal]
    I --> K[Format code with Formatter]
    J --> K[Format code with Formatter]
    K --> L[Return generated code]
    L --> M[End]
```

- **Custom Optimizer/Formatter**: Inserted at the `Optimize AST nodes` and `Format code with Formatter` steps if provided.
- **Resource Embedding**: Handled during `Merge PHP and resource files`.
