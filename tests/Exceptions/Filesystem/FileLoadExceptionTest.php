<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileLoadException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileLoadException
 */
class FileLoadExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new FileLoadException('File load error', '/path/to/file.txt');

		$this->assertInstanceOf(FileLoadException::class, $exception);
		$this->assertInstanceOf(FileException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('File load error', $exception->getMessage());
		$this->assertEquals('/path/to/file.txt', $exception->getFullPath());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new FileLoadException('Base', '/path');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
