<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions;

use Hubbitus\HuPHP\Exceptions\ProcessException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\ProcessException
**/
class ProcessExceptionTest extends TestCase {
	public function testConstructorCreatesInstance(): void {
		$exception = new ProcessException('Test message', 42);

		$this->assertInstanceOf(ProcessException::class, $exception);
		$this->assertEquals('Test message', $exception->getMessage());
		$this->assertEquals(42, $exception->getCode());
	}

	public function testConstructorWithDefaultValues(): void {
		$exception = new ProcessException();

		$this->assertInstanceOf(ProcessException::class, $exception);
		$this->assertEquals('', $exception->getMessage());
		$this->assertEquals(0, $exception->getCode());
	}

	public function testConstructorWithState(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Test message', 0, $state);

		$this->assertInstanceOf(ProcessException::class, $exception);
		$this->assertEquals('Test message', $exception->getMessage());
		$this->assertSame($state, $exception->state);
	}

	public function testConstructorWithArrayMessage(): void {
		$message = ['error' => 'test'];
		$exception = new ProcessException($message);

		$this->assertStringContainsString('error', $exception->getMessage());
		$this->assertStringContainsString('test', $exception->getMessage());
	}

	public function testConstructorWithObjectMessage(): void {
		$message = (object)['prop' => 'value'];
		$exception = new ProcessException($message);

		$this->assertStringContainsString('prop', $exception->getMessage());
		$this->assertStringContainsString('value', $exception->getMessage());
	}
}
