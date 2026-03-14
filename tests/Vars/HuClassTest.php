<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

use Hubbitus\HuPHP\Vars\HuClass;
use Hubbitus\HuPHP\Vars\Settings\Settings;
use PHPUnit\Framework\TestCase;

class HuClassTest extends TestCase {
	public function testCloning(): void {
		$original = new Settings(['key' => 'value']);
		$clone = HuClass::cloning($original);

		$this->assertEquals($original, $clone);
		$this->assertNotSame($original, $clone);

		// Modify clone should not affect original
		$clone->key = 'new_value';
		$this->assertEquals('value', $original->key);
		$this->assertEquals('new_value', $clone->key);
	}

	public function testReinterpretCast(): void {
		$source = new Settings(['name' => 'test', 'value' => 123]);

		// Cast to SettingsCheck which extends Settings
		$result = HuClass::reinterpretCast(Settings::class, $source);

		$this->assertInstanceOf(Settings::class, $result);
		$this->assertEquals('test', $result->name);
		$this->assertEquals(123, $result->value);
	}
}
