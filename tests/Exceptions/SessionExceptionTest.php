<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions;

use Hubbitus\HuPHP\Exceptions\SessionException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\SessionException
**/
class SessionExceptionTest extends TestCase {
    public function testConstructorWithNoArguments(): void {
        $exception = new SessionException();

        $this->assertInstanceOf(SessionException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new SessionException('Session error');

        $this->assertInstanceOf(SessionException::class, $exception);
        $this->assertEquals('Session error', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new SessionException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(SessionException::class);

        throw new SessionException('Session failed');
    }
}