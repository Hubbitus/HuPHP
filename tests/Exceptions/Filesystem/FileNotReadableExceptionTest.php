<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\Filesystem\FileNotReadableException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileNotReadableException
**/
class FileNotReadableExceptionTest extends TestCase {
    public function testConstructorWithNoArguments(): void {
        $exception = new FileNotReadableException();

        $this->assertInstanceOf(FileNotReadableException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new FileNotReadableException('File not readable');

        $this->assertInstanceOf(FileNotReadableException::class, $exception);
        $this->assertEquals('File not readable', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new FileNotReadableException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(FileNotReadableException::class);

        throw new FileNotReadableException('Cannot read file');
    }
}