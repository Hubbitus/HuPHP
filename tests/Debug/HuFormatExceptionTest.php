<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\HuFormatException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\HuFormatException
 */
class HuFormatExceptionTest extends TestCase {
    public function testClassExists(): void {
        $this->assertTrue(class_exists(HuFormatException::class));
    }

    public function testExtendsVariableException(): void {
        $exception = new HuFormatException();
        $this->assertInstanceOf(VariableException::class, $exception);
    }

    public function testCanBeConstructedWithoutArguments(): void {
        $exception = new HuFormatException();
        $this->assertInstanceOf(HuFormatException::class, $exception);
    }

    public function testCanBeConstructedWithMessage(): void {
        $exception = new HuFormatException('Test message');
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testCanBeConstructedWithMessageAndCode(): void {
        $exception = new HuFormatException('Error', 400);
        $this->assertEquals('Error', $exception->getMessage());
        $this->assertEquals(400, $exception->getCode());
    }

    public function testCanBeConstructedWithMessageAndPreviousException(): void {
        $previous = new \Exception('Previous');
        $exception = new HuFormatException('Current', 0, $previous);
        $this->assertEquals('Previous', $exception->getPrevious()->getMessage());
    }

    public function testThrownAndCaught(): void {
        try {
            throw new HuFormatException('Test exception');
        } catch (HuFormatException $e) {
            $this->assertEquals('Test exception', $e->getMessage());
        }
    }

    public function testCaughtAsVariableException(): void {
        try {
            throw new HuFormatException('Test');
        } catch (VariableException $e) {
            $this->assertInstanceOf(HuFormatException::class, $e);
            $this->assertEquals('Test', $e->getMessage());
        }
    }

    public function testCaughtAsException(): void {
        try {
            throw new HuFormatException('Test');
        } catch (\Exception $e) {
            $this->assertInstanceOf(HuFormatException::class, $e);
            $this->assertEquals('Test', $e->getMessage());
        }
    }

    public function testDefaultProperties(): void {
        $exception = new HuFormatException();
        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
        $this->assertNotNull($exception->getFile());
        $this->assertNull($exception->getPrevious());
    }
}
