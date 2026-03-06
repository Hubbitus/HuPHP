<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\System\OutputType;
use Hubbitus\HuPHP\Debug\HuLOGTextSettings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\HuLOGTextSettings
 */
class HuLOGTextSettingsTest extends TestCase {
    public function testConstructorWithNoArguments(): void {
        $settings = new HuLOGTextSettings();
        $this->assertInstanceOf(HuLOGTextSettings::class, $settings);
    }

    public function testExtendsHuErrorSettings(): void {
        $settings = new HuLOGTextSettings();
        $this->assertInstanceOf(\Hubbitus\HuPHP\Debug\HuErrorSettings::class, $settings);
    }

    public function testExtendsSettings(): void {
        $settings = new HuLOGTextSettings();
        $this->assertInstanceOf(\Hubbitus\HuPHP\Vars\Settings\Settings::class, $settings);
    }

    public function testDefaultAutoDate(): void {
        $settings = new HuLOGTextSettings();
        $this->assertTrue($settings->AUTO_DATE);
    }

    public function testDefaultDateFormat(): void {
        $settings = new HuLOGTextSettings();
        $this->assertEquals('Y-m-d H:i:s', $settings->DATE_FORMAT);
    }

    public function testHasConsoleFormat(): void {
        $settings = new HuLOGTextSettings();
        $format = $settings->{OutputType::CONSOLE->name};
        $this->assertIsArray($format);
    }

    public function testHasWebFormat(): void {
        $settings = new HuLOGTextSettings();
        $format = $settings->{OutputType::WEB->name};
        $this->assertIsArray($format);
    }

    public function testHasFileFormat(): void {
        $settings = new HuLOGTextSettings();
        $format = $settings->{OutputType::FILE->name};
        $this->assertIsArray($format);
    }

    public function testSetCustomDateFormat(): void {
        $settings = new HuLOGTextSettings();
        $settings->setSetting('DATE_FORMAT', 'd.m.Y H:i:s');
        $this->assertEquals('d.m.Y H:i:s', $settings->DATE_FORMAT);
    }

    public function testSetAutoDateFalse(): void {
        $settings = new HuLOGTextSettings();
        $settings->setSetting('AUTO_DATE', false);
        $this->assertFalse($settings->AUTO_DATE);
    }

    public function testSetCustomConsoleFormat(): void {
        $settings = new HuLOGTextSettings();
        $customFormat = ['custom', 'format'];
        $settings->setSetting(OutputType::CONSOLE->name, $customFormat);
        $this->assertEquals($customFormat, $settings->{OutputType::CONSOLE->name});
    }

    public function testSetCustomWebFormat(): void {
        $settings = new HuLOGTextSettings();
        $customFormat = ['web', 'format'];
        $settings->setSetting(OutputType::WEB->name, $customFormat);
        $this->assertEquals($customFormat, $settings->{OutputType::WEB->name});
    }

    public function testSetCustomFileFormat(): void {
        $settings = new HuLOGTextSettings();
        $customFormat = ['file', 'format'];
        $settings->setSetting(OutputType::FILE->name, $customFormat);
        $this->assertEquals($customFormat, $settings->{OutputType::FILE->name});
    }

    public function testLengthGreaterThanZero(): void {
        $settings = new HuLOGTextSettings();
        $this->assertGreaterThan(0, $settings->length());
    }

    public function testGetDefaultWebFormat(): void {
        $settings = new HuLOGTextSettings();
        $this->assertIsArray($settings->getDefaultWebFormat());
    }

    public function testGetDefaultConsoleFormat(): void {
        $settings = new HuLOGTextSettings();
        $this->assertIsArray($settings->getDefaultConsoleFormat());
    }

    public function testGetDefaultFileFormat(): void {
        $settings = new HuLOGTextSettings();
        $this->assertIsArray($settings->getDefaultFileFormat());
    }

    public function testIsAutoDateEnabled(): void {
        $settings = new HuLOGTextSettings();
        $this->assertTrue($settings->isAutoDateEnabled());
    }

    public function testGetDateFormat(): void {
        $settings = new HuLOGTextSettings();
        $this->assertEquals('Y-m-d H:i:s', $settings->getDateFormat());
    }
}