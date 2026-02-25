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

	public function testConstructorWithArray(): void {
		$settings = new HuLOGTextSettings(['DATE_FORMAT' => 'Y-m-d']);

		$this->assertInstanceOf(HuLOGTextSettings::class, $settings);
	}
}