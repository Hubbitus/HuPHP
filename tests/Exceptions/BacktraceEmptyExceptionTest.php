<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions;

use Hubbitus\HuPHP\Exceptions\BacktraceEmptyException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\BacktraceEmptyException
**/
class BacktraceEmptyExceptionTest extends TestCase {
	public function testConstructorCreatesInstance(): void {
		$exception = new BacktraceEmptyException('Test message', 42);

		$this->assertInstanceOf(BacktraceEmptyException::class, $exception);
		$this->assertEquals('Test message', $exception->getMessage());
		$this->assertEquals(42, $exception->getCode());
	}

	public function testConstructorWithDefaultValues(): void {
		$exception = new BacktraceEmptyException();

		$this->assertInstanceOf(BacktraceEmptyException::class, $exception);
		$this->assertEquals('', $exception->getMessage());
		$this->assertEquals(0, $exception->getCode());
	}
}
