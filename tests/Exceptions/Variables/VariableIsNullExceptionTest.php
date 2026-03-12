<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Variables;

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Exceptions\Variables\VariableException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableIsNullException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableIsNullException
**/
class VariableIsNullExceptionTest extends TestCase {
	public function testClassExists(): void {
		$this->assertTrue(\class_exists(VariableIsNullException::class));
	}

	public function testExtendsVariableRequiredException(): void {
		$exception = new VariableIsNullException();
		$this->assertInstanceOf(VariableRequiredException::class, $exception);
	}

	public function testExtendsVariableExceptionHierarchy(): void {
		$exception = new VariableIsNullException();
		$this->assertInstanceOf(VariableException::class, $exception);
	}

	public function testCanBeConstructedWithoutArguments(): void {
		$exception = new VariableIsNullException();
		$this->assertInstanceOf(VariableIsNullException::class, $exception);
	}

	public function testCanBeConstructedWithBacktrace(): void {
		$bt = new Backtrace(null, 0);
		$exception = new VariableIsNullException($bt);
		$this->assertInstanceOf(VariableIsNullException::class, $exception);
		$this->assertNotNull($exception->bt);
	}

	public function testCanBeConstructedWithBacktraceAndVarname(): void {
		$bt = new Backtrace(null, 0);
		$exception = new VariableIsNullException($bt, 'testVar');
		$this->assertInstanceOf(VariableIsNullException::class, $exception);
		$this->assertEquals('testVar', $exception->varName(true));
	}

	public function testCanBeConstructedWithAllParameters(): void {
		$bt = new Backtrace(null, 0);
		$exception = new VariableIsNullException($bt, 'testVar', 'Custom message', 400);
		$this->assertEquals('Custom message', $exception->getMessage());
		$this->assertEquals(400, $exception->getCode());
		$this->assertEquals('testVar', $exception->varName(true));
	}

	public function testCanBeConstructedWithPreviousException(): void {
		$bt = new Backtrace(null, 0);
		$previous = new \Exception('Previous');
		// Note: VariableRequiredException doesn't support previous exception in constructor
		$exception = new VariableIsNullException($bt, 'testVar', 'Current', 0);
		$this->assertEquals('Current', $exception->getMessage());
	}

	public function testThrownAndCaught(): void {
		try {
			throw new VariableIsNullException();
		} catch (VariableIsNullException $e) {
			$this->assertInstanceOf(VariableIsNullException::class, $e);
		}
	}

	public function testCaughtAsVariableRequiredException(): void {
		try {
			throw new VariableIsNullException();
		} catch (VariableRequiredException $e) {
			$this->assertInstanceOf(VariableIsNullException::class, $e);
		}
	}

	public function testCaughtAsVariableException(): void {
		try {
			throw new VariableIsNullException();
		} catch (VariableException $e) {
			$this->assertInstanceOf(VariableIsNullException::class, $e);
		}
	}

	public function testCaughtAsException(): void {
		try {
			throw new VariableIsNullException();
		} catch (\Exception $e) {
			$this->assertInstanceOf(VariableIsNullException::class, $e);
		}
	}

	public function testBacktracePropertyIsNullByDefault(): void {
		$exception = new VariableIsNullException();
		$this->assertNull($exception->bt);
	}
}
