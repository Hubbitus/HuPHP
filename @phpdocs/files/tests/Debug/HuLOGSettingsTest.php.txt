<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\HuLOGSettings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\HuLOGSettings
 */
class HuLOGSettingsTest extends TestCase {
    public function testConstructor(): void {
        $settings = new HuLOGSettings();

        $this->assertInstanceOf(HuLOGSettings::class, $settings);
    }
}
