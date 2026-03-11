<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Network;

use Hubbitus\HuPHP\Exceptions\Network\SocketWriteException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Network\SocketWriteException
**/
class SocketWriteExceptionTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$exception = new SocketWriteException();

		$this->assertInstanceOf(SocketWriteException::class, $exception);
	}

	public function testConstructorWithMessage(): void {
		$exception = new SocketWriteException('Cannot write to socket');

		$this->assertInstanceOf(SocketWriteException::class, $exception);
		$this->assertEquals('Cannot write to socket', $exception->getMessage());
	}

	public function testIsThrowable(): void {
		$exception = new SocketWriteException();

		$this->assertInstanceOf(\Throwable::class, $exception);
	}

	public function testExceptionCanBeThrown(): void {
		$this->expectException(SocketWriteException::class);

		throw new SocketWriteException('Socket write failed');
	}
}
