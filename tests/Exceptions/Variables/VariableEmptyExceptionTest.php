<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Variables;

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException
**/
class VariableEmptyExceptionTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$exception = new VariableEmptyException(new Backtrace(), );

		$this->assertInstanceOf(VariableEmptyException::class, $exception);
	}

	public function testConstructorWithMessage(): void {
		$exception = new VariableEmptyException(new Backtrace(), null, 'Variable is empty');

		$this->assertInstanceOf(VariableEmptyException::class, $exception);
		$this->assertEquals('Variable is empty', $exception->getMessage());
	}

	public function testIsThrowable(): void {
		$exception = new VariableEmptyException(new Backtrace(), );

		$this->assertInstanceOf(\Throwable::class, $exception);
	}

	public function testExceptionCanBeThrown(): void {
		$this->expectException(VariableEmptyException::class);

		throw new VariableEmptyException(new Backtrace(), null, 'Variable is empty');
	}
}