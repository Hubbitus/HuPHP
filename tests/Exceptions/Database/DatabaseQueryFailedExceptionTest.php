<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Database;

use Hubbitus\HuPHP\Exceptions\Database\DatabaseQueryFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Database\DatabaseQueryFailedException
 */
class DatabaseQueryFailedExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void {
        $exception = new DatabaseQueryFailedException();

        $this->assertInstanceOf(DatabaseQueryFailedException::class, $exception);
        $this->assertInstanceOf(\Hubbitus\HuPHP\Exceptions\Database\DatabaseException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new DatabaseQueryFailedException('Query failed');

        $this->assertInstanceOf(DatabaseQueryFailedException::class, $exception);
        $this->assertEquals('Query failed', $exception->getMessage());
    }

    public function testConstructorWithMessageAndCode(): void {
        $exception = new DatabaseQueryFailedException('Query failed', 500);

        $this->assertInstanceOf(DatabaseQueryFailedException::class, $exception);
        $this->assertEquals('Query failed', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
    }

    public function testIsThrowable(): void {
        $exception = new DatabaseQueryFailedException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(DatabaseQueryFailedException::class);
        $this->expectExceptionMessage('Database query failed');

        throw new DatabaseQueryFailedException('Database query failed');
    }

    public function testExceptionCanBeCaught(): void {
        try {
            throw new DatabaseQueryFailedException('Test exception');
        } catch (DatabaseQueryFailedException $e) {
            $this->assertInstanceOf(DatabaseQueryFailedException::class, $e);
            $this->assertEquals('Test exception', $e->getMessage());
        }
    }
}