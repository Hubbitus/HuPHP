<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassUnknownException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassUnknownException
 */
class ClassUnknownExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new ClassUnknownException('Unknown class error');

		$this->assertInstanceOf(ClassUnknownException::class, $exception);
		$this->assertInstanceOf(ClassException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Unknown class error', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new ClassUnknownException('Base');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
