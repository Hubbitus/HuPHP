<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Vars\Settings\Settings;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
	public function testConstructorEmpty(): void
	{
		$settings = new Settings();
		$this->assertInstanceOf(Settings::class, $settings);
		$this->assertEquals(0, $settings->length());
	}

	public function testConstructorWithArray(): void
	{
		$settings = new Settings(['key1' => 'value1', 'key2' => 'value2']);
		$this->assertInstanceOf(Settings::class, $settings);
		$this->assertEquals(2, $settings->length());
		$this->assertEquals('value1', $settings->key1);
		$this->assertEquals('value2', $settings->key2);
	}

	public function testSetSetting(): void
	{
		$settings = new Settings();
		$result = $settings->setSetting('key', 'value');
		$this->assertSame($settings, $result);
		$this->assertEquals('value', $settings->key);
	}

	public function testSetSettingsArray(): void
	{
		$settings = new Settings(['old' => 'old_value']);
		$settings->setSettingsArray(['new1' => 'value1', 'new2' => 'value2']);
		$this->assertEquals(2, $settings->length());
		$this->assertEquals('value1', $settings->new1);
		$this->assertEquals('value2', $settings->new2);
	}

	public function testMergeSettingsArray(): void
	{
		$settings = new Settings(['key1' => 'value1']);
		$settings->mergeSettingsArray(['key2' => 'value2', 'key1' => 'new_value']);
		$this->assertEquals(2, $settings->length());
		$this->assertEquals('new_value', $settings->key1);
		$this->assertEquals('value2', $settings->key2);
	}

	public function testGetProperty(): void
	{
		$settings = new Settings(['key' => 'value']);
		$result = $settings->getProperty('key');
		$this->assertEquals('value', $result);
	}

	public function testMagicSet(): void
	{
		$settings = new Settings();
		$settings->key = 'value';
		$this->assertEquals('value', $settings->key);
	}

	public function testMagicGet(): void
	{
		$settings = new Settings(['key' => 'value']);
		$result = $settings->key;
		$this->assertEquals('value', $result);
	}

	public function testMagicIsset(): void
	{
		$settings = new Settings(['key' => 'value']);
		$this->assertTrue(isset($settings->key));
		$this->assertFalse(isset($settings->nonexistent));
	}

	public function testGetString(): void
	{
		$settings = new Settings(['name' => 'John', 'age' => 30]);
		$fields = ['name', 'age'];
		$result = $settings->getString($fields);
		$this->assertIsString($result);
		$this->assertStringContainsString('John', $result);
		$this->assertStringContainsString('30', $result);
	}

	public function testFormatFieldString(): void
	{
		$settings = new Settings(['tag' => 'div']);
		$result = $settings->formatField('tag');
		$this->assertEquals('div', $result);
	}

	public function testFormatFieldArray(): void
	{
		$settings = new Settings(['tag' => 'div']);
		$result = $settings->formatField(['tag', '<', '>', '<unknown>']);
		$this->assertEquals('<div>', $result);
	}

	public function testFormatFieldArrayWithDefault(): void
	{
		$settings = new Settings([]);
		$result = $settings->formatField(['nonexistent', '<', '>', '<unknown>']);
		$this->assertEquals('<unknown>', $result);
	}

	public function testClear(): void
	{
		$settings = new Settings(['key1' => 'value1', 'key2' => 'value2']);
		$result = $settings->clear();
		$this->assertSame($settings, $result);
		$this->assertEquals(0, $settings->length());
	}

	public function testLength(): void
	{
		$settings = new Settings(['a' => 1, 'b' => 2, 'c' => 3]);
		$this->assertEquals(3, $settings->length());
	}
}
