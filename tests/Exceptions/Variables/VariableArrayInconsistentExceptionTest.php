<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Variables;

use Hubbitus\HuPHP\Exceptions\Variables\VariableArrayInconsistentException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableArrayInconsistentException
 */
class VariableArrayInconsistentExceptionTest extends TestCase {
    public function testClassExists(): void {
        $this->assertTrue(class_exists(VariableArrayInconsistentException::class));
    }

    public function testExtendsVariableException(): void {
        $exception = new VariableArrayInconsistentException();
        $this->assertInstanceOf(VariableException::class, $exception);
    }

    public function testCanBeConstructedWithoutArguments(): void {
        $exception = new VariableArrayInconsistentException();
        $this->assertInstanceOf(VariableArrayInconsistentException::class, $exception);
    }

    public function testCanBeConstructedWithMessage(): void {
        $exception = new VariableArrayInconsistentException('Test message');
        $this->assertEquals('Test message', $exception->getMessage());
    }

    public function testCanBeConstructedWithMessageAndCode(): void {
        $exception = new VariableArrayInconsistentException('Error', 500);
        $this->assertEquals('Error', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
    }

    public function testCanBeConstructedWithPreviousException(): void {
        $previous = new \Exception('Previous');
        $exception = new VariableArrayInconsistentException('Current', 0, $previous);
        $this->assertEquals('Previous', $exception->getPrevious()->getMessage());
    }

    public function testThrownAndCaught(): void {
        try {
            throw new VariableArrayInconsistentException('Test exception');
        } catch (VariableArrayInconsistentException $e) {
            $this->assertEquals('Test exception', $e->getMessage());
        }
    }

    public function testCaughtAsVariableException(): void {
        try {
            throw new VariableArrayInconsistentException('Test');
        } catch (VariableException $e) {
            $this->assertInstanceOf(VariableArrayInconsistentException::class, $e);
            $this->assertEquals('Test', $e->getMessage());
        }
    }

    public function testCaughtAsException(): void {
        try {
            throw new VariableArrayInconsistentException('Test');
        } catch (\Exception $e) {
            $this->assertInstanceOf(VariableArrayInconsistentException::class, $e);
            $this->assertEquals('Test', $e->getMessage());
        }
    }
}
