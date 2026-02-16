<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\HuArray;
use PHPUnit\Framework\TestCase;

class HuArrayTest extends TestCase {
    public function testConstructor(): void {
        $arr = new HuArray([1, 2, 3]);
        $this->assertInstanceOf(HuArray::class, $arr);
        $this->assertEquals([1, 2, 3], $arr->getArray());
    }

    public function testPush(): void {
        $arr = new HuArray([1]);
        $arr->push(2, 3);
        $this->assertEquals([1, 2, 3], $arr->getArray());
    }

    public function testLast(): void {
        $arr = new HuArray([1, 2, 3]);
        $last = &$arr->last();
        $last = 99;
        $this->assertEquals([1, 2, 99], $arr->getArray());
    }
}
