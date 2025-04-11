<?php

namespace PhpPacker\Generator\Tests\Exception;

use PhpPacker\Generator\Exception\GeneratorException;
use PHPUnit\Framework\TestCase;

class GeneratorExceptionTest extends TestCase
{
    public function testIsRuntimeException(): void
    {
        $exception = new GeneratorException('Test message');
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testConstructorWithMessage(): void
    {
        $message = 'Test generator exception message';
        $exception = new GeneratorException($message);

        $this->assertEquals($message, $exception->getMessage());
    }

    public function testConstructorWithCode(): void
    {
        $code = 123;
        $exception = new GeneratorException('Test message', $code);

        $this->assertEquals($code, $exception->getCode());
    }

    public function testConstructorWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new GeneratorException('Test message', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
