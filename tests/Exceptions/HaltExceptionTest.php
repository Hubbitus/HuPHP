<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions;

use Hubbitus\HuPHP\Exceptions\HaltException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\HaltException
**/
class HaltExceptionTest extends TestCase {
	public function testConstructorCreatesInstance(): void {
		$exception = new HaltException('Test message', 42);

		$this->assertInstanceOf(HaltException::class, $exception);
		$this->assertEquals('Test message', $exception->getMessage());
		$this->assertEquals(42, $exception->getCode());
	}

	public function testConstructorWithDefaultValues(): void {
		$exception = new HaltException();

		$this->assertInstanceOf(HaltException::class, $exception);
		$this->assertEquals('', $exception->getMessage());
		$this->assertEquals(0, $exception->getCode());
	}
}
