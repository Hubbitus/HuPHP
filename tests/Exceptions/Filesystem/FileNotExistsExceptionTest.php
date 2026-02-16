<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileLoadException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileNotExistsException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileNotExistsException
 */
class FileNotExistsExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new FileNotExistsException('File does not exist', '/path/to/file.txt');

		$this->assertInstanceOf(FileNotExistsException::class, $exception);
		$this->assertInstanceOf(FileLoadException::class, $exception);
		$this->assertInstanceOf(FileException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('File does not exist', $exception->getMessage());
		$this->assertEquals('/path/to/file.txt', $exception->getFullPath());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new FileNotExistsException('Base', '/path');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
