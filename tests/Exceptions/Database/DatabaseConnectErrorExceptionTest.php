<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Database;

use Hubbitus\HuPHP\Exceptions\Database\DatabaseConnectErrorException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Database\DatabaseConnectErrorException
 */
class DatabaseConnectErrorExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void {
        $exception = new DatabaseConnectErrorException();

        $this->assertInstanceOf(DatabaseConnectErrorException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new DatabaseConnectErrorException('Connection failed');

        $this->assertInstanceOf(DatabaseConnectErrorException::class, $exception);
        $this->assertEquals('Connection failed', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new DatabaseConnectErrorException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(DatabaseConnectErrorException::class);

        throw new DatabaseConnectErrorException('Cannot connect to database');
    }
}