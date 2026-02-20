<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\Filesystem\FileRemoteGetException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileRemoteGetException
 */
class FileRemoteGetExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void {
        $exception = new FileRemoteGetException();

        $this->assertInstanceOf(FileRemoteGetException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new FileRemoteGetException('Remote get failed');

        $this->assertInstanceOf(FileRemoteGetException::class, $exception);
        $this->assertEquals('Remote get failed', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new FileRemoteGetException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(FileRemoteGetException::class);

        throw new FileRemoteGetException('Cannot get remote file');
    }
}
