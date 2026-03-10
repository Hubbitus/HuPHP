<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Variables;

use Hubbitus\HuPHP\Exceptions\Variables\VariableException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableException
**/
class VariableExceptionTest extends TestCase {
    public function testConstructorWithNoArguments(): void {
        $exception = new VariableException();

        $this->assertInstanceOf(VariableException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new VariableException('Variable error');

        $this->assertInstanceOf(VariableException::class, $exception);
        $this->assertEquals('Variable error', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new VariableException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(VariableException::class);

        throw new VariableException('Variable operation failed');
    }
}
