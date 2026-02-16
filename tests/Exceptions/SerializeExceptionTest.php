<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\SerializeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\SerializeException
 */
class SerializeExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new SerializeException('Serialization failed');

		$this->assertInstanceOf(SerializeException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Serialization failed', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new SerializeException('Base');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
