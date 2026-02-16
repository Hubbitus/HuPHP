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
		$this->assertEquals('/path/to/file.txt', $exception->getFullPath());
	}

	public function testFullPathProperty(): void {
		$path = '/var/log/error.log';
		$exception = new FileException('Error', $path);

		$this->assertEquals($path, $exception->getFullPath());
	}

	public function testToString(): void {
		$exception = new FileException('Test error', '/test/path');
		$string = (string) $exception;

		$this->assertStringContainsString('FileException', $string);
		$this->assertStringContainsString('/test/path', $string);
		$this->assertStringContainsString('Test error', $string);
	}

	public function testToStringFormat(): void {
		$exception = new FileException('Error message', '/some/path/file.php');
		$string = (string) $exception;

		$this->assertMatchesRegularExpression('/FileException:\s*\[\/some\/path\/file\.php\]:\s*Error message/', $string);
	}

	public function testEmptyPath(): void {
		$exception = new FileException('Error', '');

		$this->assertEquals('', $exception->getFullPath());
		$string = (string) $exception;
		$this->assertStringContainsString('[]:', $string);
	}
}
