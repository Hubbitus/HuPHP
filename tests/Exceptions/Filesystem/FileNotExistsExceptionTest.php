<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\Filesystem\FileNotExistsException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileNotExistsException
 */
class FileNotExistsExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void {
        $exception = new FileNotExistsException();

        $this->assertInstanceOf(FileNotExistsException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new FileNotExistsException('File not found');

        $this->assertInstanceOf(FileNotExistsException::class, $exception);
        $this->assertEquals('File not found', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new FileNotExistsException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(FileNotExistsException::class);

        throw new FileNotExistsException('File does not exist');
    }
}