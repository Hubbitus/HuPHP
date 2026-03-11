<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Vars\Settings\SettingsGet;
use PHPUnit\Framework\TestCase;

class SettingsGetTest extends TestCase {
	public function testMagicGetSettings(): void {
		$settingsArray = ['key' => 'value'];

		$settingsGet = new class($settingsArray) extends SettingsGet {
			public function __construct($sets) {
				$this->_sets = $sets;
			}
		};

		$result = $settingsGet->settings;
		$this->assertSame($settingsArray, $result);
		$this->assertEquals('value', $result['key']);
	}

	public function testSetsMethod(): void {
		$settingsArray = ['key' => 'value'];

		$settingsGet = new class($settingsArray) extends SettingsGet {
			public function __construct($sets) {
				$this->_sets = $sets;
			}
		};

		$result = $settingsGet->sets();
		$this->assertSame($settingsArray, $result);
		$this->assertEquals('value', $result['key']);
	}

	public function testSetsReturnsReference(): void {
		$settingsArray = ['key' => 'value'];

		$settingsGet = new class($settingsArray) extends SettingsGet {
			public function __construct($sets) {
				$this->_sets = $sets;
			}
		};

		$ref = &$settingsGet->sets();
		$ref['key'] = 'new_value';

		$this->assertEquals('new_value', $settingsGet->sets()['key']);
	}

	public function testMagicGetArrayElement(): void {
		$settingsArray = ['key' => 'value', 'other' => 'data'];

		$settingsGet = new class($settingsArray) extends SettingsGet {
			public function __construct($sets) {
				$this->_sets = $sets;
			}
		};

		// Test accessing array element via __get
		$result = $settingsGet->key;
		$this->assertEquals('value', $result);

		$result = $settingsGet->other;
		$this->assertEquals('data', $result);
	}
}
