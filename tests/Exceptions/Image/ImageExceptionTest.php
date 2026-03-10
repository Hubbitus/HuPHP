<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Image;

use Hubbitus\HuPHP\Exceptions\Image\ImageException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Image\ImageException
**/
class ImageExceptionTest extends TestCase {
    public function testConstructorWithNoArguments(): void {
        $exception = new ImageException();

        $this->assertInstanceOf(ImageException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new ImageException('Image error');

        $this->assertInstanceOf(ImageException::class, $exception);
        $this->assertEquals('Image error', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new ImageException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(ImageException::class);

        throw new ImageException('Image operation failed');
    }
}