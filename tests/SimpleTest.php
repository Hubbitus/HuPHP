<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
* Simple smoke test to verify PHPUnit is working.
**/
class SimpleTest extends TestCase {
    public function testTrueIsTrue(): void {
        $this->assertTrue(true);
    }
}