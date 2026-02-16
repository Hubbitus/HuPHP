<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\HuLOGSettings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\HuLOGSettings
 */
class HuLOGSettingsTest extends TestCase {
	public function testConstantsDefined(): void {
		$reflection = new \ReflectionClass(HuLOGSettings::class);
		$this->assertTrue($reflection->hasConstant('LOG_TO_FILE'));
		$this->assertTrue($reflection->hasConstant('LOG_TO_PRINT'));
		$this->assertTrue($reflection->hasConstant('LOG_TO_BOTH'));
	}

	public function testConstantsValues(): void {
		$this->assertEquals(8, HuLOGSettings::LOG_TO_FILE);
		$this->assertEquals(4, HuLOGSettings::LOG_TO_PRINT);
		$this->assertEquals(12, HuLOGSettings::LOG_TO_BOTH);
	}

	public function testConstructorWithNoArguments(): void {
		$settings = new HuLOGSettings();

		$this->assertInstanceOf(HuLOGSettings::class, $settings);
	}

	public function testConstructorWithArray(): void {
		$settings = new HuLOGSettings(['FILE_PREFIX' => 'custom_']);

		$this->assertInstanceOf(HuLOGSettings::class, $settings);
	}
}
