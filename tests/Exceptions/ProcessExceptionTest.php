<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\ProcessException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\ProcessException
 */
class ProcessExceptionTest extends TestCase {
	public function testConstructor(): void {
		$mockState = new \stdClass();
		$mockState->value = 'test';

		$exception = new ProcessException('Process error', 0, $mockState);

		$this->assertInstanceOf(ProcessException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Process error', $exception->getMessage());
		$this->assertEquals(0, $exception->getCode());
		$this->assertSame($mockState, $exception->state);
	}

	public function testStateProperty(): void {
		$mockState = new \stdClass();
		$exception = new ProcessException('Error', 0, $mockState);

		$this->assertObjectHasProperty('state', $exception);
		$this->assertSame($mockState, $exception->state);
	}

	public function testInheritsBaseExceptionMethods(): void {
		$mockState = new \stdClass();
		$exception = new ProcessException('Base', 0, $mockState);
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}

	public function testConstructorWithNullMessage(): void {
		$mockState = new \stdClass();
		$exception = new ProcessException(null, 0, $mockState);

		$this->assertInstanceOf(ProcessException::class, $exception);
		$this->assertEquals('', $exception->getMessage());
	}
}
