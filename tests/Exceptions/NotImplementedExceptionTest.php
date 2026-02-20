<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions;

use Hubbitus\HuPHP\Exceptions\NotImplementedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\NotImplementedException
 */
class NotImplementedExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void {
        $exception = new NotImplementedException();

        $this->assertInstanceOf(NotImplementedException::class, $exception);
    }

    public function testConstructorWithMessage(): void {
        $exception = new NotImplementedException('Not implemented');

        $this->assertInstanceOf(NotImplementedException::class, $exception);
        $this->assertEquals('Not implemented', $exception->getMessage());
    }

    public function testIsThrowable(): void {
        $exception = new NotImplementedException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(NotImplementedException::class);

        throw new NotImplementedException('Feature not implemented');
    }
}