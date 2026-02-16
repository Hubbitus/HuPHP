<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\NullClass;
use PHPUnit\Framework\TestCase;

class NullClassTest extends TestCase
{
    public function testConstructor(): void
    {
        $null = new NullClass();
        $this->assertInstanceOf(NullClass::class, $null);
    }
}