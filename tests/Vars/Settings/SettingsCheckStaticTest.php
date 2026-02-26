<?php
declare(strict_types=1);

/**
 * Test for SettingsCheckStatic class.
 */

namespace Hubbitus\HuPHP\Tests\Vars\Settings;

use Hubbitus\HuPHP\Vars\Settings\SettingsCheckStatic;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\Settings\SettingsCheckStatic
 */
class SettingsCheckStaticTest extends TestCase {
    public function testConstructor(): void {
        $possibles = ['prop1' => null, 'prop2' => null];
        $settings = new SettingsCheckStatic($possibles);
        
        $this->assertInstanceOf(SettingsCheckStatic::class, $settings);
    }

    public function testSetPropertiesAfterConstruction(): void {
        $possibles = ['prop1' => null, 'prop2' => null];
        $settings = new SettingsCheckStatic($possibles);
        $settings->prop1 = 'value1';
        $settings->prop2 = 'value2';
        
        $this->assertEquals('value1', $settings->prop1);
        $this->assertEquals('value2', $settings->prop2);
    }

    public function testGetRegularKeys(): void {
        $possibles = ['prop1' => null, 'prop2' => null, 'staticProp' => null];
        $settings = new SettingsCheckStatic($possibles);
        $settings->prop1 = 'value1';
        $settings->prop2 = 'value2';
        $settings->staticProp = 'staticValue';
        
        // Mark staticProp as static
        $reflection = new \ReflectionClass($settings);
        $staticSettingsProperty = $reflection->getProperty('static_settings');
        $staticSettingsProperty->setAccessible(true);
        $staticSettingsProperty->setValue($settings, ['staticProp']);
        
        $regularKeys = $settings->getRegularKeys();
        
        $this->assertIsArray($regularKeys);
        $this->assertContains('prop1', $regularKeys);
        $this->assertContains('prop2', $regularKeys);
        $this->assertNotContains('staticProp', $regularKeys);
    }

    public function testGetRegularKeysWithNoStaticSettings(): void {
        $possibles = ['prop1' => null, 'prop2' => null];
        $settings = new SettingsCheckStatic($possibles);
        $settings->prop1 = 'value1';
        $settings->prop2 = 'value2';
        
        $regularKeys = $settings->getRegularKeys();
        
        $this->assertIsArray($regularKeys);
        $this->assertCount(2, $regularKeys);
        $this->assertContains('prop1', $regularKeys);
        $this->assertContains('prop2', $regularKeys);
    }

    public function testClearMethod(): void {
        $possibles = ['prop1' => null, 'prop2' => null, 'staticProp' => null];
        $settings = new SettingsCheckStatic($possibles);
        $settings->prop1 = 'value1';
        $settings->prop2 = 'value2';
        $settings->staticProp = 'staticValue';
        
        // Mark staticProp as static
        $reflection = new \ReflectionClass($settings);
        $staticSettingsProperty = $reflection->getProperty('static_settings');
        $staticSettingsProperty->setAccessible(true);
        $staticSettingsProperty->setValue($settings, ['staticProp']);
        
        $result = $settings->clear();
        
        $this->assertSame($settings, $result);
        $this->assertNull($settings->prop1);
        $this->assertNull($settings->prop2);
        $this->assertEquals('staticValue', $settings->staticProp);
    }

    public function testClearMethodWithEmptySettings(): void {
        $possibles = ['prop1' => null, 'prop2' => null];
        $settings = new SettingsCheckStatic($possibles);
        
        $result = $settings->clear();
        
        $this->assertSame($settings, $result);
    }

    public function testClearMethodReturnsReference(): void {
        $possibles = ['prop1' => null];
        $settings = new SettingsCheckStatic($possibles);
        $settings->prop1 = 'value1';
        
        $result = $settings->clear();
        $result->prop1 = 'newValue';
        
        $this->assertEquals('newValue', $settings->prop1);
    }

    public function testInheritsFromSettingsCheck(): void {
        $possibles = ['prop1' => null];
        $settings = new SettingsCheckStatic($possibles);
        
        $this->assertInstanceOf(\Hubbitus\HuPHP\Vars\Settings\SettingsCheck::class, $settings);
    }

    public function testInheritsFromSettings(): void {
        $possibles = ['prop1' => null];
        $settings = new SettingsCheckStatic($possibles);
        
        $this->assertInstanceOf(\Hubbitus\HuPHP\Vars\Settings\Settings::class, $settings);
    }

    public function testStaticSettingsPropertyIsProtected(): void {
        $possibles = ['prop1' => null];
        $settings = new SettingsCheckStatic($possibles);
        
        $reflection = new \ReflectionClass($settings);
        $property = $reflection->getProperty('static_settings');
        
        $this->assertTrue($property->isProtected());
    }
}
