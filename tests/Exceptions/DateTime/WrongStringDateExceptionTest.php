<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions\DateTime;

use Hubbitus\HuPHP\Exceptions\DateTime\WrongStringDateException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\DateTime\WrongStringDateException
 */
class WrongStringDateExceptionTest extends TestCase
{
    public function testConstructorWithNoArguments(): void
    {
        $exception = new WrongStringDateException();

        $this->assertInstanceOf(WrongStringDateException::class, $exception);
    }

    public function testConstructorWithMessage(): void
    {
        $exception = new WrongStringDateException('Invalid date format');

        $this->assertInstanceOf(WrongStringDateException::class, $exception);
        $this->assertEquals('Invalid date format', $exception->getMessage());
    }

    public function testIsThrowable(): void
    {
        $exception = new WrongStringDateException();

        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(WrongStringDateException::class);

        throw new WrongStringDateException('Wrong date string');
    }
}