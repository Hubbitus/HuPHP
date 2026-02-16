<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\Backtrace;
use PHPUnit\Framework\TestCase;

class BacktraceTest extends TestCase
{
    public function testConstructor(): void
    {
        $bt = new Backtrace();
        $this->assertInstanceOf(Backtrace::class, $bt);
    }
}