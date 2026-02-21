<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\Classes\ClassUnknownException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassUnknownException
 */
class ClassUnknownExceptionTest extends TestCase {
    public function testConstructorWithNoArguments(): void {
        $exception = new ClassUnknownException();

        $this->assertInstanceOf(ClassUnknownException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new ClassUnknownException('Unknown class');

        $this->assertInstanceOf(ClassUnknownException::class, $exception);
        $this->assertEquals('Unknown class', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new ClassUnknownException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(ClassUnknownException::class);

        throw new ClassUnknownException('Unknown class error');
    }
}