<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassException
 */
class ClassExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new ClassException('Class error');

		$this->assertInstanceOf(ClassException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Class error', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new ClassException('Base message');
		$exception->ADDMessage(' - additional');

		$this->assertEquals('Base message - additional', $exception->getMessage());
	}

	public function testAddMessageAtBeginning(): void {
		$exception = new ClassException('message');
		$exception->ADDMessage('Prefix: ', true);

		$this->assertEquals('Prefix: message', $exception->getMessage());
	}
}
