<?php

namespace PhpPacker\Generator\Resource;

use PhpPacker\Generator\Exception\GeneratorException;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\MagicConst\Dir;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use Psr\Log\LoggerInterface;

class ResourceHolderGenerator
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * 为资源文件生成相应的代码节点
     *
     * @param string $resFile 资源文件路径
     * @return \Traversable 生成的代码节点
     * @throws GeneratorException 当资源文件不存在时
     */
    public function generateResourceHolder(string $resFile): \Traversable
    {
        $this->logger->debug('Generating resource holder for ' . $resFile);

        // 验证资源文件是否存在
        if (!file_exists($resFile)) {
            throw new GeneratorException("Resource file not found: $resFile");
        }

        yield new Expression(
            new Assign(
                new Variable('fileName'),
                new Concat(
                    new Dir(),
                    new String_('/' . basename($resFile)),
                ),
            ),
        );

        $md5Hash = md5_file($resFile);
        yield new If_(
            new BooleanAnd(
                new FuncCall(new Name('\file_exists'), [
                    new Arg(new Variable('fileName')),
                ]),
                new NotIdentical(
                    new FuncCall(new Name('\md5_file'), [
                        new Arg(new Variable('fileName')),
                    ]),
                    new String_($md5Hash),
                ),
            ),
            [
                'stmts' => [
                    new Expression(
                        new FuncCall(new Name('\unlink'), [
                            new Arg(new Variable('fileName')),
                        ]),
                    ),
                ],
            ],
        );

        $bContent = base64_encode(file_get_contents($resFile));
        yield new If_(
            new BooleanNot(
                new FuncCall(new Name('\file_exists'), [
                    new Arg(new Variable('fileName')),
                ]),
            ),
            [
                'stmts' => [
                    new Expression(
                        new FuncCall(new Name('\file_put_contents'), [
                            new Arg(new Variable('fileName')),
                            new FuncCall(
                                new Name('\base64_decode'),
                                [
                                    new Arg(new String_($bContent)),
                                ],
                            ),
                        ]),
                    ),
                ],
            ],
        );
    }
}
