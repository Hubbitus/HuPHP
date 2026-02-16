<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileLoadException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileNotReadableException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileNotReadableException
 */
class FileNotReadableExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new FileNotReadableException('File not readable', '/path/to/file.txt');

		$this->assertInstanceOf(FileNotReadableException::class, $exception);
		$this->assertInstanceOf(FileLoadException::class, $exception);
		$this->assertInstanceOf(FileException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('File not readable', $exception->getMessage());
		$this->assertEquals('/path/to/file.txt', $exception->getFullPath());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new FileNotReadableException('Base', '/path');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
