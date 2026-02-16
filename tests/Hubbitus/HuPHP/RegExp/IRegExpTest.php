<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\RegExp;

use PHPUnit\Framework\TestCase;

class IRegExpTest extends TestCase
{
    public function testInterfaceExists(): void
    {
        $this->assertTrue(interface_exists('Hubbitus\HuPHP\RegExp\IRegExp'));
    }
}