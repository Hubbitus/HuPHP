<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Network;

use Hubbitus\HuPHP\Exceptions\Network\SocketOpenException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Network\SocketOpenException
 */
class SocketOpenExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void {
        $exception = new SocketOpenException();

        $this->assertInstanceOf(SocketOpenException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new SocketOpenException('Cannot open socket');

        $this->assertInstanceOf(SocketOpenException::class, $exception);
        $this->assertEquals('Cannot open socket', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new SocketOpenException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(SocketOpenException::class);

        throw new SocketOpenException('Socket open failed');
    }
}
