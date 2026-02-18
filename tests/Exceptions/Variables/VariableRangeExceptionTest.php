<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Variables;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException
 */
class VariableRangeExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void
    {
        $exception = new VariableRangeException();

        $this->assertInstanceOf(VariableRangeException::class, $exception);
    }

    public function testConstructorWithMessage(): void
    {
        $exception = new VariableRangeException('Value out of range');

        $this->assertInstanceOf(VariableRangeException::class, $exception);
        $this->assertEquals('Value out of range', $exception->getMessage());
    }

    public function testIsThrowable(): void
    {
        $exception = new VariableRangeException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(VariableRangeException::class);

        throw new VariableRangeException('Value is out of valid range');
    }
}