<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\DateTime;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\DateTime\WrongStringDateException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\DateTime\WrongStringDateException
 */
class WrongStringDateExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new WrongStringDateException('Invalid date string');

		$this->assertInstanceOf(WrongStringDateException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Invalid date string', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new WrongStringDateException('Base');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
