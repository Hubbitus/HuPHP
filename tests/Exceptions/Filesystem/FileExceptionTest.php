<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\Filesystem\FileException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileException
**/
class FileExceptionTest extends TestCase {
	public function testConstructorCreatesInstance(): void {
		$exception = new FileException('Test message', '/path/to/file.txt', 42);

		$this->assertInstanceOf(FileException::class, $exception);
		$this->assertEquals('Test message', $exception->getMessage());
		$this->assertEquals(42, $exception->getCode());
	}

	public function testConstructorWithDefaultValues(): void {
		$exception = new FileException();

		$this->assertInstanceOf(FileException::class, $exception);
		$this->assertEquals('', $exception->getMessage());
		$this->assertEquals('', $exception->getFullPath());
		$this->assertEquals(0, $exception->getCode());
	}

	public function testGetFullPath(): void {
		$exception = new FileException('Error', '/test/file.txt');

		$this->assertEquals('/test/file.txt', $exception->getFullPath());
	}

	public function testToString(): void {
		$exception = new FileException('Error message', '/path/to/file.txt', 42);
		$string = (string) $exception;

		$this->assertStringContainsString('Error message', $string);
		$this->assertStringContainsString('/path/to/file.txt', $string);
		// Code is not included in __toString output
	}
}
