<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\SessionException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\SessionException
 */
class SessionExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new SessionException('Session error');

		$this->assertInstanceOf(SessionException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Session error', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new SessionException('Base');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
