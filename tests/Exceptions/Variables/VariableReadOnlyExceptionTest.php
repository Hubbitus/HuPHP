<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Variables;

use Hubbitus\HuPHP\Exceptions\Variables\VariableReadOnlyException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableReadOnlyException
**/
class VariableReadOnlyExceptionTest extends TestCase {
    public function testConstructorWithNoArguments(): void {
        $exception = new VariableReadOnlyException();

        $this->assertInstanceOf(VariableReadOnlyException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new VariableReadOnlyException('Variable is read-only');

        $this->assertInstanceOf(VariableReadOnlyException::class, $exception);
        $this->assertEquals('Variable is read-only', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new VariableReadOnlyException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(VariableReadOnlyException::class);

        throw new VariableReadOnlyException('Cannot modify read-only variable');
    }
}