<?php
declare(strict_types=1);

/**
 * Test for DatabaseException class.
 */

namespace Hubbitus\HuPHP\Tests\Exceptions\Database;

use Hubbitus\HuPHP\Exceptions\Database\DatabaseException;
use Hubbitus\HuPHP\Database\Database;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Database\DatabaseException
 */
class DatabaseExceptionTest extends TestCase {
    public function testClassExtendsBaseException(): void {
        $exception = new DatabaseException('Test message');
        $this->assertInstanceOf(DatabaseException::class, $exception);
        $this->assertInstanceOf(\Hubbitus\HuPHP\Exceptions\BaseException::class, $exception);
    }

    public function testClassExtendsException(): void {
        $exception = new DatabaseException('Test message');
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionMessage(): void {
        $exception = new DatabaseException('Custom error message');
        $this->assertEquals('Custom error message', $exception->getMessage());
    }

    public function testExceptionCode(): void {
        $exception = new DatabaseException('Error', 42);
        $this->assertEquals(42, $exception->getCode());
    }

    public function testExceptionCodeDefault(): void {
        $exception = new DatabaseException('Error');
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithDatabase(): void {
        $db = $this->createMock(Database::class);
        $exception = new DatabaseException('DB error', 0, $db);
        
        $this->assertSame($db, $exception->db);
    }

    public function testExceptionWithNullDatabase(): void {
        $exception = new DatabaseException('DB error');
        
        $this->assertNull($exception->db);
    }

    public function testExceptionWithPrevious(): void {
        $previous = new \Exception('Previous error');
        $exception = new DatabaseException('Current error', 0, null, $previous);
        
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionWithAllParameters(): void {
        $db = $this->createMock(Database::class);
        $previous = new \Exception('Previous', 10);
        $exception = new DatabaseException('Full test', 99, $db, $previous);
        
        $this->assertEquals('Full test', $exception->getMessage());
        $this->assertEquals(99, $exception->getCode());
        $this->assertSame($db, $exception->db);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionFile(): void {
        $exception = new DatabaseException('Error');
        $file = $exception->getFile();
        
        $this->assertIsString($file);
        $this->assertNotEmpty($file);
    }

    public function testExceptionLine(): void {
        $exception = new DatabaseException('Error');
        $line = $exception->getLine();
        
        $this->assertIsInt($line);
        $this->assertGreaterThan(0, $line);
    }

    public function testExceptionTrace(): void {
        $exception = new DatabaseException('Error');
        $trace = $exception->getTrace();
        
        $this->assertIsArray($trace);
    }

    public function testExceptionTraceAsString(): void {
        $exception = new DatabaseException('Error');
        $traceString = $exception->getTraceAsString();
        
        $this->assertIsString($traceString);
    }

    public function testExceptionToString(): void {
        $exception = new DatabaseException('Error message');
        $string = (string) $exception;
        
        $this->assertIsString($string);
        $this->assertStringContainsString('Error message', $string);
    }

    public function testExceptionImplementsThrowable(): void {
        $exception = new DatabaseException('Error');
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(DatabaseException::class);
        $this->expectExceptionMessage('Test exception');

        throw new DatabaseException('Test exception');
    }

    public function testExceptionThrownWithDatabase(): void {
        $db = $this->createMock(Database::class);
        
        $this->expectException(DatabaseException::class);
        $this->expectExceptionMessage('DB connection failed');

        throw new DatabaseException('DB connection failed', 0, $db);
    }

    public function testAddMessageInherited(): void {
        $exception = new DatabaseException('Initial');
        $exception->ADDMessage(' Added');
        
        $this->assertEquals('Initial Added', $exception->getMessage());
    }

    public function testSerialization(): void {
        $exception = new DatabaseException('Serialize test');
        $serialized = serialize($exception);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(DatabaseException::class, $unserialized);
        $this->assertEquals('Serialize test', $unserialized->getMessage());
    }
}
