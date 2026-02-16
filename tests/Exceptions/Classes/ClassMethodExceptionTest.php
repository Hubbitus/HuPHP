<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassMethodException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassMethodException
 */
class ClassMethodExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new ClassMethodException('Method does not exist');

		$this->assertInstanceOf(ClassMethodException::class, $exception);
		$this->assertInstanceOf(ClassException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Method does not exist', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new ClassMethodException('Base');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
