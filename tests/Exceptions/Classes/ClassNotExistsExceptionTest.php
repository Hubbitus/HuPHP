<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\Classes\ClassNotExistsException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassNotExistsException
**/
class ClassNotExistsExceptionTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$exception = new ClassNotExistsException();

		$this->assertInstanceOf(ClassNotExistsException::class, $exception);
	}

	public function testConstructorWithMessage(): void {
		$exception = new ClassNotExistsException('Class not found');

		$this->assertInstanceOf(ClassNotExistsException::class, $exception);
		$this->assertEquals('Class not found', $exception->getMessage());
	}

	public function testIsThrowable(): void {
		$exception = new ClassNotExistsException();

		$this->assertInstanceOf(\Throwable::class, $exception);
	}

	public function testExceptionCanBeThrown(): void {
		$this->expectException(ClassNotExistsException::class);

		throw new ClassNotExistsException('Class does not exist');
	}
}