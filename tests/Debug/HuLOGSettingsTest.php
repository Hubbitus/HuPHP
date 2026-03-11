<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\HuLOGSettings;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Debug\HuLOGSettings
**/
class HuLOGSettingsTest extends TestCase {
	public function testConstructor(): void {
		$settings = new HuLOGSettings();

		$this->assertInstanceOf(HuLOGSettings::class, $settings);
	}

	public function testConstantsExist(): void {
		$this->assertEquals(4, HuLOGSettings::LOG_TO_PRINT);
		$this->assertEquals(8, HuLOGSettings::LOG_TO_FILE);
		$this->assertEquals(12, HuLOGSettings::LOG_TO_BOTH);
	}

	public function testLogToBothIsSumOfFileAndPrint(): void {
		$this->assertEquals(
			HuLOGSettings::LOG_TO_FILE + HuLOGSettings::LOG_TO_PRINT,
			HuLOGSettings::LOG_TO_BOTH
		);
	}

	public function testDefaultFilePrefix(): void {
		$settings = new HuLOGSettings();
		$this->assertEquals('log_', $settings->FILE_PREFIX);
	}

	public function testDefaultLogFileDir(): void {
		$settings = new HuLOGSettings();
		$this->assertEquals('./log/', $settings->LOG_FILE_DIR);
	}

	public function testDefaultLogToAcs(): void {
		$settings = new HuLOGSettings();
		$this->assertEquals(HuLOGSettings::LOG_TO_BOTH, $settings->LOG_TO_ACS);
	}

	public function testDefaultLogToErr(): void {
		$settings = new HuLOGSettings();
		$this->assertEquals(HuLOGSettings::LOG_TO_BOTH, $settings->LOG_TO_ERR);
	}

	public function testConstructorWithCustomSettings(): void {
		$settings = new HuLOGSettings([
			'FILE_PREFIX' => 'custom_',
			'LOG_FILE_DIR' => '/custom/log/',
		]);

		$this->assertEquals('custom_', $settings->FILE_PREFIX);
		$this->assertEquals('/custom/log/', $settings->LOG_FILE_DIR);
	}

	public function testSetCustomLogToAcs(): void {
		$settings = new HuLOGSettings();
		$settings->setSetting('LOG_TO_ACS', HuLOGSettings::LOG_TO_FILE);

		$this->assertEquals(HuLOGSettings::LOG_TO_FILE, $settings->LOG_TO_ACS);
	}

	public function testSetCustomLogToErr(): void {
		$settings = new HuLOGSettings();
		$settings->setSetting('LOG_TO_ERR', HuLOGSettings::LOG_TO_PRINT);

		$this->assertEquals(HuLOGSettings::LOG_TO_PRINT, $settings->LOG_TO_ERR);
	}

	public function testExtendsSettings(): void {
		$settings = new HuLOGSettings();
		$this->assertInstanceOf(\Hubbitus\HuPHP\Vars\Settings\Settings::class, $settings);
	}

	public function testHasLengthMethod(): void {
		$settings = new HuLOGSettings();
		$this->assertTrue(\method_exists($settings, 'length'));
		$this->assertGreaterThan(0, $settings->length());
	}

	public function testCanSetHuLOGTextSettings(): void {
		$settings = new HuLOGSettings();
		$settings->setSetting('HuLOG_Text_settings', ['custom' => 'value']);

		$this->assertEquals(['custom' => 'value'], $settings->HuLOG_Text_settings);
	}
}
