<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\NotImplementedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\NotImplementedException
 */
class NotImplementedExceptionTest extends TestCase {
	public function testConstructor(): void {
		$exception = new NotImplementedException('Method not implemented');

		$this->assertInstanceOf(NotImplementedException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Method not implemented', $exception->getMessage());
	}

	public function testInheritsBaseExceptionMethods(): void {
		$exception = new NotImplementedException('Base');
		$exception->ADDMessage(' - additional');

		$this->assertEquals('Base - additional', $exception->getMessage());
	}

	public function testAddMessageAtBeginning(): void {
		$exception = new NotImplementedException('message');
		$exception->ADDMessage('TODO: ', true);

		$this->assertEquals('TODO: message', $exception->getMessage());
	}
}
