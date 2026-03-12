<?php
declare(strict_types=1);

use Hubbitus\HuPHP\Debug\HuFormatException;
use PHPUnit\Framework\TestCase;

/**
* Class HuFormatExceptionTest.
**/
class HuFormatExceptionTest extends TestCase {
	public function testExceptionClass(): void {
		$this->assertInstanceOf(Exception::class, new HuFormatException('Test message'));
	}

	public function testExceptionMessage(): void {
		$exception = new HuFormatException('Test message');
		$this->assertSame('Test message', $exception->getMessage());
	}

	public function testExceptionCode(): void {
		$exception = new HuFormatException('Test message');
		$this->assertSame(0, $exception->getCode());
	}

	public function testExceptionTrace(): void {
		$exception = new HuFormatException('Test message');
		$this->assertNotEmpty($exception->getTrace());
	}
}
