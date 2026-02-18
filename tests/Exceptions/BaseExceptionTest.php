<?php
declare(strict_types=1);

/**
 * Test for BaseException class.
 */

namespace Hubbitus\HuPHP\Tests\Exceptions;

use Hubbitus\HuPHP\Exceptions\BaseException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\BaseException
 */
class BaseExceptionTest extends TestCase
{
    public function testClassInstantiation(): void
    {
        $exception = new BaseException('Test message');
        $this->assertInstanceOf(BaseException::class, $exception);
    }

    public function testClassExtendsException(): void
    {
        $exception = new BaseException('Test message');
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionMessage(): void
    {
        $exception = new BaseException('Custom error message');
        $this->assertEquals('Custom error message', $exception->getMessage());
    }

    public function testExceptionCode(): void
    {
        $exception = new BaseException('Error', 42);
        $this->assertEquals(42, $exception->getCode());
    }

    public function testExceptionCodeDefault(): void
    {
        $exception = new BaseException('Error');
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionFile(): void
    {
        $exception = new BaseException('Error');
        $file = $exception->getFile();
        $this->assertIsString($file);
        $this->assertNotEmpty($file);
    }

    public function testExceptionLine(): void
    {
        $exception = new BaseException('Error');
        $line = $exception->getLine();
        $this->assertIsInt($line);
        $this->assertGreaterThan(0, $line);
    }

    public function testExceptionTrace(): void
    {
        $exception = new BaseException('Error');
        $trace = $exception->getTrace();
        $this->assertIsArray($trace);
    }

    public function testExceptionTraceAsString(): void
    {
        $exception = new BaseException('Error');
        $traceString = $exception->getTraceAsString();
        $this->assertIsString($traceString);
    }

    public function testExceptionToString(): void
    {
        $exception = new BaseException('Error message');
        $string = (string) $exception;
        $this->assertIsString($string);
        $this->assertStringContainsString('Error message', $string);
    }

    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous error');
        $exception = new BaseException('Current error', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testAddMessageAtEnd(): void
    {
        $exception = new BaseException('Initial');
        $exception->ADDMessage(' Added');
        $this->assertEquals('Initial Added', $exception->getMessage());
    }

    public function testAddMessageAtBeginning(): void
    {
        $exception = new BaseException('Initial');
        $exception->ADDMessage('Prefix ', true);
        $this->assertEquals('Prefix Initial', $exception->getMessage());
    }

    public function testAddMessageMultipleTimes(): void
    {
        $exception = new BaseException('Start');
        $exception->ADDMessage(' Middle');
        $exception->ADDMessage(' End');
        $this->assertEquals('Start Middle End', $exception->getMessage());
    }

    public function testAddMessageWithEmptyString(): void
    {
        $exception = new BaseException('Message');
        $exception->ADDMessage('');
        $this->assertEquals('Message', $exception->getMessage());
    }

    public function testAddMessageWithSpecialCharacters(): void
    {
        $exception = new BaseException('Test');
        $exception->ADDMessage(' @#$% ^&*() ');
        $this->assertEquals('Test @#$% ^&*() ', $exception->getMessage());
    }

    public function testAddMessageWithNewlines(): void
    {
        $exception = new BaseException('Line1');
        $exception->ADDMessage("\nLine2");
        $this->assertEquals("Line1\nLine2", $exception->getMessage());
    }

    public function testAddMessageWithUnicode(): void
    {
        $exception = new BaseException('Hello');
        $exception->ADDMessage(' Мир');
        $this->assertEquals('Hello Мир', $exception->getMessage());
    }

    public function testAddMessageDoesNotReturn(): void
    {
        $exception = new BaseException('Test');
        $result = $exception->ADDMessage(' Added');
        $this->assertNull($result);
    }

    public function testAddMessageWithLongString(): void
    {
        $exception = new BaseException('Short');
        $exception->ADDMessage(str_repeat('Long ', 100));
        $this->assertStringContainsString('Short', $exception->getMessage());
    }

    public function testExceptionImplementsThrowable(): void
    {
        $exception = new BaseException('Error');
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(BaseException::class);
        $this->expectExceptionMessage('Test exception');
        
        throw new BaseException('Test exception');
    }

    public function testExceptionWithCodeAndPrevious(): void
    {
        $previous = new \Exception('Previous', 10);
        $exception = new BaseException('Current', 20, $previous);
        
        $this->assertEquals(20, $exception->getCode());
        $this->assertEquals('Current', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
        $this->assertEquals(10, $previous->getCode());
    }

    public function testAddMessagePreservesType(): void
    {
        $exception = new BaseException('String');
        $exception->ADDMessage('123');
        $this->assertIsString($exception->getMessage());
    }

    public function testExceptionSerialization(): void
    {
        $exception = new BaseException('Serialize test');
        $serialized = serialize($exception);
        $unserialized = unserialize($serialized);
        
        $this->assertInstanceOf(BaseException::class, $unserialized);
        $this->assertEquals('Serialize test', $unserialized->getMessage());
    }

    public function testExceptionClone(): void
    {
        $exception1 = new BaseException('Clone test');
        $exception2 = clone $exception1;
        
        $this->assertEquals($exception1->getMessage(), $exception2->getMessage());
        $this->assertEquals($exception1->getCode(), $exception2->getCode());
    }

    public function testAddMessageWithBooleanTrue(): void
    {
        $exception = new BaseException('Test');
        $exception->ADDMessage(true ? ' Yes' : ' No');
        $this->assertEquals('Test Yes', $exception->getMessage());
    }

    public function testAddMessageWithBooleanFalse(): void
    {
        $exception = new BaseException('Test');
        $exception->ADDMessage('No ', true);
        $this->assertEquals('No Test', $exception->getMessage());
    }
}
