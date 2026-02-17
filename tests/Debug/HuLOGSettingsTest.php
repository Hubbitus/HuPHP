<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\HuLOGSettings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\HuLOGSettings
 */
class HuLOGSettingsTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$settings = new HuLOGSettings();

		$this->assertInstanceOf(HuLOGSettings::class, $settings);
	}

	public function testConstructorWithArray(): void {
		$settings = new HuLOGSettings(['FILE_PREFIX' => 'custom_']);

		$this->assertInstanceOf(HuLOGSettings::class, $settings);
	}

	public function testDefaultSettings(): void {
		$settings = new HuLOGSettings();

		$this->assertEquals('log_', $settings->FILE_PREFIX);
		$this->assertEquals('./log/', $settings->LOG_FILE_DIR);
		$this->assertEquals(HuLOGSettings::LOG_TO_BOTH, $settings->LOG_TO_ACS);
		$this->assertEquals(HuLOGSettings::LOG_TO_BOTH, $settings->LOG_TO_ERR);
	}

	public function testOverrideSettings(): void {
		$settings = new HuLOGSettings([
			'FILE_PREFIX' => 'test_',
			'LOG_FILE_DIR' => '/tmp/logs/'
		]);

		$this->assertEquals('test_', $settings->FILE_PREFIX);
		$this->assertEquals('/tmp/logs/', $settings->LOG_FILE_DIR);
	}

	public function testConstants(): void {
		$this->assertEquals(8, HuLOGSettings::LOG_TO_FILE);
		$this->assertEquals(4, HuLOGSettings::LOG_TO_PRINT);
		$this->assertEquals(12, HuLOGSettings::LOG_TO_BOTH);
	}
}