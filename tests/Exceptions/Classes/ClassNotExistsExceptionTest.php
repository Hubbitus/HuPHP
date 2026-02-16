<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassNotExistsException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassNotExistsException
 */
class ClassNotExistsExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new ClassNotExistsException('Class does not exist');

		$this->assertInstanceOf(ClassNotExistsException::class, $exception);
		$this->assertInstanceOf(ClassException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Class does not exist', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new ClassNotExistsException('Base');
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
