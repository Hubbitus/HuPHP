<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\BacktraceEmptyException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\BacktraceEmptyException
**/
class BacktraceEmptyExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new BacktraceEmptyException('Backtrace is empty');

		$this->assertInstanceOf(BacktraceEmptyException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Backtrace is empty', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new BacktraceEmptyException('Base');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
