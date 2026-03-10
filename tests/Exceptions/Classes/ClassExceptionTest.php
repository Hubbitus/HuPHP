<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\Classes\ClassException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassException
**/
class ClassExceptionTest extends TestCase {
    public function testConstructorWithNoArguments(): void {
        $exception = new ClassException();

        $this->assertInstanceOf(ClassException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new ClassException('Class error');

        $this->assertInstanceOf(ClassException::class, $exception);
        $this->assertEquals('Class error', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new ClassException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(ClassException::class);

        throw new ClassException('Class operation failed');
    }
}