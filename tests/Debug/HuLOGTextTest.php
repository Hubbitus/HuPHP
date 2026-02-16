<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\HuLOGText;
use Hubbitus\HuPHP\Debug\HuLOGTextSettings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\HuLOGText
 */
class HuLOGTextTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$text = new HuLOGText([]);

		$this->assertInstanceOf(HuLOGText::class, $text);
	}

	public function testConstructorWithArray(): void {
		$settings = ['DATE_FORMAT' => 'Y-m-d'];
		$text = new HuLOGText($settings);

		$this->assertInstanceOf(HuLOGText::class, $text);
	}

	public function testConstructorWithSettingsObject(): void {
		$settings = new HuLOGTextSettings();
		$text = new HuLOGText($settings);

		$this->assertInstanceOf(HuLOGText::class, $text);
	}
}
