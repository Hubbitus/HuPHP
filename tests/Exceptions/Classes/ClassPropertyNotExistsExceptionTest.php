<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException
 */
class ClassPropertyNotExistsExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new ClassPropertyNotExistsException('Property does not exist');

		$this->assertInstanceOf(ClassPropertyNotExistsException::class, $exception);
		$this->assertInstanceOf(ClassException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Property does not exist', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new ClassPropertyNotExistsException('Base');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
