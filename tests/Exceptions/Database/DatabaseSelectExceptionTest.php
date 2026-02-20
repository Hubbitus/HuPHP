<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Database;

use Hubbitus\HuPHP\Exceptions\Database\DatabaseSelectException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Database\DatabaseSelectException
 */
class DatabaseSelectExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void {
        $exception = new DatabaseSelectException();

        $this->assertInstanceOf(DatabaseSelectException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new DatabaseSelectException('Select failed');

        $this->assertInstanceOf(DatabaseSelectException::class, $exception);
        $this->assertEquals('Select failed', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new DatabaseSelectException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(DatabaseSelectException::class);

        throw new DatabaseSelectException('Database select failed');
    }
}