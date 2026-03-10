<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\HuLOGTextSettings;
use Hubbitus\HuPHP\Debug\HuErrorSettings;
use PHPUnit\Framework\TestCase;
use Hubbitus\HuPHP\System\OutputType;

/**
* @covers \Hubbitus\HuPHP\Debug\HuLOGTextSettings
**/
final class HuLOGTextSettingsTest extends TestCase {
    public function testCanBeInstantiatedAndInheritsFromHuErrorSettings(): void {
        $settings = new HuLOGTextSettings();
        $this->assertInstanceOf(HuLOGTextSettings::class, $settings);
        $this->assertInstanceOf(HuErrorSettings::class, $settings);
    }

    public function testDefaultSettingsAreCorrectlySet(): void {
        $settings = new HuLOGTextSettings();

        // Check some default values from HuLOGTextSettings
        $this->assertTrue($settings->AUTO_DATE);
        $this->assertEquals('Y-m-d H:i:s:', $settings->DATE_FORMAT);
        $this->assertEquals('Extra info', $settings->EXTRA_HEADER);

        // Check specific format for Console output
        $consoleFormat = $settings->getProperty(OutputType::CONSOLE->name);
        $this->assertIsArray($consoleFormat);
        $this->assertCount(6, $consoleFormat); // date, level, type, logText, extra,

        $this->assertEquals(['date', "\033[36m", "\033[0m"], $consoleFormat[0]);
    }
}
