<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Network;

use Hubbitus\HuPHP\Exceptions\Network\SocketReadException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\Network\SocketReadException
**/
class SocketReadExceptionTest extends TestCase {
    public function testConstructorWithNoArguments(): void {
        $exception = new SocketReadException();

        $this->assertInstanceOf(SocketReadException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new SocketReadException('Cannot read from socket');

        $this->assertInstanceOf(SocketReadException::class, $exception);
        $this->assertEquals('Cannot read from socket', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new SocketReadException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(SocketReadException::class);

        throw new SocketReadException('Socket read failed');
    }
}
