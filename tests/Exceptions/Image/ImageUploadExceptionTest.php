<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Image;

use Hubbitus\HuPHP\Exceptions\Image\ImageUploadException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Image\ImageUploadException
**/
class ImageUploadExceptionTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$exception = new ImageUploadException();

		$this->assertInstanceOf(ImageUploadException::class, $exception);
	}

	public function testConstructorWithMessage(): void {
		$exception = new ImageUploadException('Upload failed');

		$this->assertInstanceOf(ImageUploadException::class, $exception);
		$this->assertEquals('Upload failed', $exception->getMessage());
	}

	public function testIsThrowable(): void {
		$exception = new ImageUploadException();

		$this->assertInstanceOf(\Throwable::class, $exception);
	}

	public function testExceptionCanBeThrown(): void {
		$this->expectException(ImageUploadException::class);

		throw new ImageUploadException('Image upload failed');
	}
}