<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\HuConfig;
use PHPUnit\Framework\TestCase;

class HuConfigTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists('Hubbitus\HuPHP\Vars\HuConfig'));
    }
}