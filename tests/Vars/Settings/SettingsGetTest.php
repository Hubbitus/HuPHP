<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Vars\Settings\SettingsGet;
use Hubbitus\HuPHP\Vars\Settings\Settings;
use PHPUnit\Framework\TestCase;

class SettingsGetTest extends TestCase
{
	public function testMagicGetSettings(): void
	{
		$settingsObj = new Settings(['key' => 'value']);

		$settingsGet = new class($settingsObj) extends SettingsGet {
			public function __construct($sets)
			{
				$this->_sets = $sets;
			}
		};

		$result = $settingsGet->settings;
		$this->assertSame($settingsObj, $result);
		$this->assertEquals('value', $result->key);
	}

	public function testSetsMethod(): void
	{
		$settingsObj = new Settings(['key' => 'value']);

		$settingsGet = new class($settingsObj) extends SettingsGet {
			public function __construct($sets)
			{
				$this->_sets = $sets;
			}
		};

		$result = $settingsGet->sets();
		$this->assertSame($settingsObj, $result);
		$this->assertEquals('value', $result->key);
	}

	public function testSetsReturnsReference(): void
	{
		$settingsObj = new Settings(['key' => 'value']);

		$settingsGet = new class($settingsObj) extends SettingsGet {
			public function __construct($sets)
			{
				$this->_sets = $sets;
			}
		};

		$ref = &$settingsGet->sets();
		$ref->key = 'new_value';

		$this->assertEquals('new_value', $settingsGet->sets()->key);
	}
}
