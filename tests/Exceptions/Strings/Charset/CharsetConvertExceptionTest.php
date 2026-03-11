<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Strings\Charset;

use Hubbitus\HuPHP\Exceptions\Strings\Charset\CharsetConvertException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Strings\Charset\CharsetConvertException
**/
class CharsetConvertExceptionTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$exception = new CharsetConvertException();

		$this->assertInstanceOf(CharsetConvertException::class, $exception);
	}

	public function testConstructorWithMessage(): void {
		$exception = new CharsetConvertException('Charset conversion failed');

		$this->assertInstanceOf(CharsetConvertException::class, $exception);
		$this->assertEquals('Charset conversion failed', $exception->getMessage());
	}

	public function testIsThrowable(): void {
		$exception = new CharsetConvertException();

		$this->assertInstanceOf(\Throwable::class, $exception);
	}

	public function testExceptionCanBeThrown(): void {
		$this->expectException(CharsetConvertException::class);

		throw new CharsetConvertException('Cannot convert charset');
	}
}