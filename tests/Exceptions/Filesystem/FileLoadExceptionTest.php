<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\Filesystem\FileLoadException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileLoadException
 */
class FileLoadExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void
    {
        $exception = new FileLoadException();

        $this->assertInstanceOf(FileLoadException::class, $exception);
    }

    public function testConstructorWithMessage(): void
    {
        $exception = new FileLoadException('Cannot load file');

        $this->assertInstanceOf(FileLoadException::class, $exception);
        $this->assertEquals('Cannot load file', $exception->getMessage());
    }

    public function testIsThrowable(): void
    {
        $exception = new FileLoadException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(FileLoadException::class);

        throw new FileLoadException('File load failed');
    }
}