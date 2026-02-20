<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Network;

use Hubbitus\HuPHP\Exceptions\Network\NetworkException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Network\NetworkException
 */
class NetworkExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void {
        $exception = new NetworkException();

        $this->assertInstanceOf(NetworkException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new NetworkException('Network error');

        $this->assertInstanceOf(NetworkException::class, $exception);
        $this->assertEquals('Network error', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new NetworkException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(NetworkException::class);

        throw new NetworkException('Network operation failed');
    }
}
