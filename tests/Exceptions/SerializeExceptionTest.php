<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions;

use Hubbitus\HuPHP\Exceptions\SerializeException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\SerializeException
**/
class SerializeExceptionTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$exception = new SerializeException();

		$this->assertInstanceOf(SerializeException::class, $exception);
	}

	public function testConstructorWithMessage(): void {
		$exception = new SerializeException('Serialize error');

		$this->assertInstanceOf(SerializeException::class, $exception);
		$this->assertEquals('Serialize error', $exception->getMessage());
	}

	public function testIsThrowable(): void {
		$exception = new SerializeException();

		$this->assertInstanceOf(\Throwable::class, $exception);
	}

	public function testExceptionCanBeThrown(): void {
		$this->expectException(SerializeException::class);

		throw new SerializeException('Serialization failed');
	}
}