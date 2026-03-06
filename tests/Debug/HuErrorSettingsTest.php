<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\HuErrorSettings;
use Hubbitus\HuPHP\System\OutputType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\HuErrorSettings
 */
final class HuErrorSettingsTest extends TestCase {
    public function testConstructorCreatesInstance(): void {
        $settings = new HuErrorSettings();

        $this->assertInstanceOf(HuErrorSettings::class, $settings);
    }

    public function testConstructorCallsInitDefaults(): void {
        $settings = new HuErrorSettings();

        // Verify defaults were set by initDefaults()
        $this->assertIsArray($settings->WEB);
        $this->assertIsArray($settings->CONSOLE);
        $this->assertIsArray($settings->FILE);
        $this->assertTrue($settings->AUTO_DATE);
        $this->assertSame('Y-m-d H:i:s', $settings->DATE_FORMAT);
    }

    public function testConstructorSetsDefaultFormats(): void {
        $settings = new HuErrorSettings();

        $this->assertIsArray($settings->WEB);
        $this->assertIsArray($settings->CONSOLE);
        $this->assertIsArray($settings->FILE);
    }

    public function testConstructorSetsDefaultFormatValues(): void {
        $settings = new HuErrorSettings();

        $this->assertSame([], $settings->WEB);
        $this->assertSame([], $settings->CONSOLE);
        $this->assertSame([], $settings->FILE);
    }

    public function testConstructorSetsAutoDate(): void {
        $settings = new HuErrorSettings();

        $this->assertTrue($settings->AUTO_DATE);
    }

    public function testConstructorSetsDateFormat(): void {
        $settings = new HuErrorSettings();

        $this->assertSame('Y-m-d H:i:s', $settings->DATE_FORMAT);
    }

    public function testCanModifySettings(): void {
        $settings = new HuErrorSettings();

        $settings->AUTO_DATE = false;
        $this->assertFalse($settings->AUTO_DATE);

        $settings->DATE_FORMAT = 'd.m.Y H:i:s';
        $this->assertSame('d.m.Y H:i:s', $settings->DATE_FORMAT);
    }

    public function testCanSetCustomFormats(): void {
        $settings = new HuErrorSettings();

        $customFormat = [['date'], 'level', ['type']];
        $settings->WEB = $customFormat;

        $this->assertSame($customFormat, $settings->WEB);
    }

    public function testSetSettingMethod(): void {
        $settings = new HuErrorSettings();

        $settings->setSetting('TEST_SETTING', 'test_value');

        $this->assertSame('test_value', $settings->TEST_SETTING);
    }

    public function testMergeSettingsArray(): void {
        $settings = new HuErrorSettings();

        $settings->mergeSettingsArray([
            'AUTO_DATE' => false,
            'DATE_FORMAT' => 'd.m.Y',
        ]);

        $this->assertFalse($settings->AUTO_DATE);
        $this->assertSame('d.m.Y', $settings->DATE_FORMAT);
    }

    public function testGetProperty(): void {
        $settings = new HuErrorSettings();

        $autoDate = $settings->getProperty('AUTO_DATE');

        $this->assertTrue($autoDate);
    }

    public function testSetSettingReturnsSelf(): void {
        $settings = new HuErrorSettings();

        $result = $settings->setSetting('TEST', 'value');

        $this->assertSame($settings, $result);
    }

    public function testGetDefaultWebFormat(): void {
        $settings = new HuErrorSettings();

        $format = $settings->getDefaultWebFormat();

        $this->assertIsArray($format);
        $this->assertEmpty($format);
    }

    public function testGetDefaultConsoleFormat(): void {
        $settings = new HuErrorSettings();

        $format = $settings->getDefaultConsoleFormat();

        $this->assertIsArray($format);
        $this->assertEmpty($format);
    }

    public function testGetDefaultFileFormat(): void {
        $settings = new HuErrorSettings();

        $format = $settings->getDefaultFileFormat();

        $this->assertIsArray($format);
        $this->assertEmpty($format);
    }

    public function testIsAutoDateEnabled(): void {
        $settings = new HuErrorSettings();

        $this->assertTrue($settings->isAutoDateEnabled());
    }

    public function testGetDateFormat(): void {
        $settings = new HuErrorSettings();

        $this->assertSame('Y-m-d H:i:s', $settings->getDateFormat());
    }
}
