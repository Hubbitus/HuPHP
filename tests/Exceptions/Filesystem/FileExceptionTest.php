<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileException
 */
class FileExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new FileException('File not found', '/path/to/file.txt');

		$this->assertInstanceOf(FileException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('File not found', $exception->getMessage());
	}

	public function testGetFullPath(): void {
		$exception = new FileException('Error', '/var/log/error.log');

		$this->assertEquals('/var/log/error.log', $exception->getFullPath());
	}

	public function testToString(): void {
		$exception = new FileException('Test error', '/test/path');
		$string = (string) $exception;

		$this->assertStringContainsString('FileException', $string);
		$this->assertStringContainsString('/test/path', $string);
		$this->assertStringContainsString('Test error', $string);
	}
}