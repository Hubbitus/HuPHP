<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\Classes\ClassException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassMethodException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassMethodException
**/
class ClassMethodExceptionTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$exception = new ClassMethodException();

		$this->assertInstanceOf(ClassMethodException::class, $exception);
		$this->assertInstanceOf(ClassException::class, $exception);
	}

	public function testConstructorWithMessage(): void {
		$exception = new ClassMethodException('Method not found');

		$this->assertInstanceOf(ClassMethodException::class, $exception);
		$this->assertEquals('Method not found', $exception->getMessage());
	}

	public function testConstructorWithMessageAndCode(): void {
		$exception = new ClassMethodException('Method not found', 404);

		$this->assertInstanceOf(ClassMethodException::class, $exception);
		$this->assertEquals('Method not found', $exception->getMessage());
		$this->assertEquals(404, $exception->getCode());
	}

	public function testIsThrowable(): void {
		$exception = new ClassMethodException();

		$this->assertInstanceOf(\Throwable::class, $exception);
	}

	public function testExceptionCanBeThrown(): void {
		$this->expectException(ClassMethodException::class);
		$this->expectExceptionMessage('Method does not exist');

		throw new ClassMethodException('Method does not exist');
	}

	public function testExceptionCanBeCaught(): void {
		try {
			throw new ClassMethodException('Test exception');
		} catch (ClassMethodException $e) {
			$this->assertInstanceOf(ClassMethodException::class, $e);
			$this->assertEquals('Test exception', $e->getMessage());
		}
	}
}