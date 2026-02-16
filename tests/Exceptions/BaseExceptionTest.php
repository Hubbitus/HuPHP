<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions;

use Hubbitus\HuPHP\Exceptions\BaseException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\BaseException
 */
class BaseExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new BaseException('Test message');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(\Exception::class, $exception);
		$this->assertEquals('Test message', $exception->getMessage());
	}

	public function testAddMessageAtEnd(): void {
		$exception = new BaseException('Original message');
		$exception->ADDMessage(' - additional');

		$this->assertEquals('Original message - additional', $exception->getMessage());
	}

	public function testAddMessageAtBeginning(): void {
		$exception = new BaseException('message');
		$exception->ADDMessage('Prefix: ', true);

		$this->assertEquals('Prefix: message', $exception->getMessage());
	}

	public function testAddMessageMultipleTimes(): void {
		$exception = new BaseException('base');
		$exception->ADDMessage(' suffix1');
		$exception->ADDMessage(' suffix2');

		$this->assertEquals('base suffix1 suffix2', $exception->getMessage());
	}

	public function testAddMessageAtBeginningMultipleTimes(): void {
		$exception = new BaseException('base');
		$exception->ADDMessage('Pre1: ', true);
		$exception->ADDMessage('Pre2: ', true);

		$this->assertEquals('Pre2: Pre1: base', $exception->getMessage());
	}

	public function testAddMessageMixedPositions(): void {
		$exception = new BaseException('middle');
		$exception->ADDMessage('Prefix: ', true);
		$exception->ADDMessage(' Suffix');

		$this->assertEquals('Prefix: middle Suffix', $exception->getMessage());
	}

	public function testAddMessageEmptyString(): void {
		$exception = new BaseException('Test');
		$exception->ADDMessage('');

		$this->assertEquals('Test', $exception->getMessage());
	}
}
