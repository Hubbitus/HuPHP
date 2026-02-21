<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\HuLOG;
use Hubbitus\HuPHP\Debug\HuLOGSettings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\HuLOG
 */
class HuLOGTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$log = new HuLOG();

		$this->assertInstanceOf(HuLOG::class, $log);
	}

	public function testConstructorWithArray(): void {
		$settings = ['FILE_PREFIX' => 'test_'];
		$log = new HuLOG($settings);

		$this->assertInstanceOf(HuLOG::class, $log);
	}

	public function testConstructorWithSettingsObject(): void {
		$settings = new HuLOGSettings();
		$log = new HuLOG($settings);

		$this->assertInstanceOf(HuLOG::class, $log);
	}

	public function testLevelProperty(): void {
		$log = new HuLOG();

		$this->assertObjectHasProperty('_level', $log);
		$this->assertEquals(0, $log->_level);
	}

	public function testSetLevel(): void {
		$log = new HuLOG();
		$log->_level = 2;

		$this->assertEquals(2, $log->_level);
	}
}
