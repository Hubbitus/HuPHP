<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Variables;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\Debug\Backtrace;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException
 */
class VariableRequiredExceptionTest extends TestCase
{
    public function testConstructorWithBacktrace(): void {
        $backtrace = new Backtrace();
        $exception = new VariableRequiredException($backtrace);

        $this->assertInstanceOf(VariableRequiredException::class, $exception);
        $this->assertInstanceOf(\Hubbitus\HuPHP\Exceptions\Variables\VariableException::class, $exception);
    }

    public function testConstructorWithBacktraceAndVarName(): void {
        $backtrace = new Backtrace();
        $exception = new VariableRequiredException($backtrace, 'testVariable');

        $this->assertInstanceOf(VariableRequiredException::class, $exception);
    }

    public function testConstructorWithAllArguments(): void {
        $backtrace = new Backtrace();
        $exception = new VariableRequiredException($backtrace, 'testVariable', 'Custom message');

        $this->assertInstanceOf(VariableRequiredException::class, $exception);
        $this->assertStringContainsString('Custom message', $exception->getMessage());
    }

    public function testGetMessage(): void {
        $backtrace = new Backtrace();
        $exception = new VariableRequiredException($backtrace, 'testVar', 'Variable is required');

        $this->assertStringContainsString('Variable is required', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $backtrace = new Backtrace();
        $exception = new VariableRequiredException($backtrace);

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(VariableRequiredException::class);
        $this->expectExceptionMessage('Test exception');

        throw new VariableRequiredException(new Backtrace(), 'testVar', 'Test exception');
    }

    public function testExceptionCanBeCaught(): void {
        $backtrace = new Backtrace();
        try {
            throw new VariableRequiredException($backtrace, 'testVar', 'Test exception');
        } catch (VariableRequiredException $e) {
            $this->assertInstanceOf(VariableRequiredException::class, $e);
            $this->assertStringContainsString('Test exception', $e->getMessage());
        }
    }

    public function testVarName(): void {
        $backtrace = new Backtrace();
        $exception = new VariableRequiredException($backtrace, 'myVariable', 'Variable is required');

        $this->assertEquals('myVariable', $exception->varName(true));
    }

    public function testBacktraceProperty(): void {
        $backtrace = new Backtrace();
        $exception = new VariableRequiredException($backtrace, 'testVar');

        $this->assertInstanceOf(Backtrace::class, $exception->bt);
    }
}
