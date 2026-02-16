<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\Single;
use PHPUnit\Framework\TestCase;

class TestClassForSingleton {
    public $value = 'test';
}

class SingleTest extends TestCase
{
    public function testSingleton(): void
    {
        $instance1 = Single::singleton(TestClassForSingleton::class);
        $instance2 = Single::singleton(TestClassForSingleton::class);
        
        $this->assertInstanceOf(TestClassForSingleton::class, $instance1);
        $this->assertInstanceOf(TestClassForSingleton::class, $instance2);
        $this->assertSame($instance1, $instance2);
        $this->assertEquals('test', $instance1->value);
    }
}