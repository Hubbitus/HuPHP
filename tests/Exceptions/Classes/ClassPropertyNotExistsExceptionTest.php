<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\Classes;

use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException
 */
class ClassPropertyNotExistsExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void
    {
        $exception = new ClassPropertyNotExistsException();

        $this->assertInstanceOf(ClassPropertyNotExistsException::class, $exception);
    }

    public function testConstructorWithMessage(): void
    {
        $exception = new ClassPropertyNotExistsException('Property not found');

        $this->assertInstanceOf(ClassPropertyNotExistsException::class, $exception);
        $this->assertEquals('Property not found', $exception->getMessage());
    }

    public function testIsThrowable(): void
    {
        $exception = new ClassPropertyNotExistsException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(ClassPropertyNotExistsException::class);

        throw new ClassPropertyNotExistsException('Property does not exist');
    }
}