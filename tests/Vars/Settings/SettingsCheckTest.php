<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Vars\Settings\SettingsCheck;
use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use PHPUnit\Framework\TestCase;

class SettingsCheckTest extends TestCase
{
	public function testConstructorWithPossibles(): void {
		$possibles = ['name', 'age', 'email'];
		$settings = new SettingsCheck($possibles);
		$this->assertInstanceOf(SettingsCheck::class, $settings);
		$this->assertEquals($possibles, $settings->properties);
	}

	public function testConstructorWithPossiblesAndArray(): void {
		$possibles = ['name', 'age', 'email'];
		$settings = new SettingsCheck($possibles, ['name' => 'John', 'age' => 30]);
		$this->assertEquals('John', $settings->name);
		$this->assertEquals(30, $settings->age);
	}

	public function testSetSettingValid(): void {
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles);
		$result = $settings->setSetting('name', 'John');
		$this->assertSame($settings, $result);
		$this->assertEquals('John', $settings->name);
	}

	public function testSetSettingInvalid(): void {
		$this->expectException(ClassPropertyNotExistsException::class);
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles);
		$settings->setSetting('invalid', 'value');
	}

	public function testGetPropertyValid(): void {
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles, ['name' => 'John']);
		$result = $settings->getProperty('name');
		$this->assertEquals('John', $result);
	}

	public function testGetPropertyInvalid(): void {
		$this->expectException(ClassPropertyNotExistsException::class);
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles);
		$settings->getProperty('invalid');
	}

	public function testAddSetting(): void {
		$possibles = ['name'];
		$settings = new SettingsCheck($possibles);
		$settings->addSetting('age', 30);
		$this->assertContains('age', $settings->properties);
		$this->assertEquals(30, $settings->age);
	}

	public function testSetSettingsArrayValid(): void {
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles);
		$settings->setSettingsArray(['name' => 'Jane', 'age' => 25]);
		$this->assertEquals('Jane', $settings->name);
		$this->assertEquals(25, $settings->age);
	}

	public function testSetSettingsArrayInvalid(): void {
		$this->expectException(ClassPropertyNotExistsException::class);
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles);
		$settings->setSettingsArray(['name' => 'Jane', 'invalid' => 'value']);
	}

	public function testMagicIssetValid(): void {
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles, ['name' => 'John']);
		$this->assertTrue(isset($settings->name));
	}

	public function testMagicIssetInvalid(): void {
		$this->expectException(ClassPropertyNotExistsException::class);
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles);
		isset($settings->invalid);
	}

	public function testMergeSettingsArrayValid(): void {
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles);
		$settings->mergeSettingsArray(['name' => 'John', 'age' => 30]);
		$this->assertEquals('John', $settings->name);
		$this->assertEquals(30, $settings->age);
	}

	public function testMergeSettingsArrayInvalid(): void {
		$this->expectException(ClassPropertyNotExistsException::class);
		$possibles = ['name', 'age'];
		$settings = new SettingsCheck($possibles);
		$settings->mergeSettingsArray(['name' => 'John', 'invalid' => 'value']);
	}

	public function testNesting(): void {
		$possibles = ['name', 'properties_addon'];
		$settings = new SettingsCheck($possibles);
		$settings->properties_addon = ['age', 'email'];
		$settings->nesting();
		$this->assertContains('age', $settings->properties);
		$this->assertContains('email', $settings->properties);
	}
}
