<?php

/**
 * Test for DatabaseSettings class.
 */

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseSettings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseSettings
 */
class DatabaseSettingsTest extends TestCase
{
    public function testConstructorWithDefaultSettings(): void
    {
        $settings = new DatabaseSettings();

        $this->assertInstanceOf(DatabaseSettings::class, $settings);
        $this->assertEquals('UTF-8', $settings->CHARSET_RECODE['TO']);
        $this->assertEquals('CP1251', $settings->CHARSET_RECODE['FROM']);
        $this->assertFalse($settings->DEBUG);
    }

    public function testConstructorWithCustomSettings(): void
    {
        $customSettings = [
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => 'pass',
            'dbName' => 'test_db',
            'persistent' => false,
            'charset' => 'UTF8',
            'CHARSET_RECODE' => [
                'FROM' => 'UTF8',
                'TO' => 'ISO-8859-1'
            ],
            'DEBUG' => true,
            'DBError_settings' => [
                'level' => 'error'
            ]
        ];

        $settings = new DatabaseSettings($customSettings);

        $this->assertEquals('localhost', $settings->hostname);
        $this->assertEquals('root', $settings->username);
        $this->assertEquals('pass', $settings->password);
        $this->assertEquals('test_db', $settings->dbName);
        $this->assertFalse($settings->persistent);
        $this->assertEquals('UTF8', $settings->charset);
        $this->assertEquals('UTF8', $settings->CHARSET_RECODE['FROM']);
        $this->assertEquals('ISO-8859-1', $settings->CHARSET_RECODE['TO']);
        $this->assertTrue($settings->DEBUG);
        $this->assertEquals('error', $settings->DBError_settings['level']);
    }

    public function testGettersAndSetters(): void
    {
        $settings = new DatabaseSettings();

        // Test default getters
        $this->assertEquals('UTF-8', $settings->CHARSET_RECODE['TO']);
        $this->assertFalse($settings->DEBUG);

        // Test setters
        $settings->hostname = 'testhost';
        $settings->username = 'testuser';
        $settings->password = 'testpass';
        $settings->dbName = 'testdb';
        $settings->persistent = true;
        $settings->charset = 'UTF8';
        $settings->CHARSET_RECODE['FROM'] = 'UTF8';
        $settings->CHARSET_RECODE['TO'] = 'ISO-8859-1';
        $settings->DEBUG = true;
        $settings->DBError_settings['level'] = 'debug';

        // Test getters after setting
        $this->assertEquals('testhost', $settings->hostname);
        $this->assertEquals('testuser', $settings->username);
        $this->assertEquals('testpass', $settings->password);
        $this->assertEquals('testdb', $settings->dbName);
        $this->assertTrue($settings->persistent);
        $this->assertEquals('UTF8', $settings->charset);
        $this->assertEquals('UTF8', $settings->CHARSET_RECODE['FROM']);
        $this->assertEquals('ISO-8859-1', $settings->CHARSET_RECODE['TO']);
        $this->assertTrue($settings->DEBUG);
        $this->assertEquals('debug', $settings->DBError_settings['level']);
    }

    public function testDefaultSettingsStructure(): void
    {
        $settings = new DatabaseSettings();

        // Test that all expected default settings are present
        $expectedDefaults = [
            'CHARSET_RECODE' => [
                'FROM' => 'CP1251',
                'TO' => 'UTF-8'
            ],
            'DEBUG' => false,
            'DBError_settings' => []
        ];

        foreach ($expectedDefaults as $key => $value) {
            $this->assertArrayHasKey($key, $settings->getArrayCopy());
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $this->assertEquals($subValue, $settings->$key[$subKey]);
                }
            } else {
                $this->assertEquals($value, $settings->$key);
            }
        }
    }

    public function testInheritanceFromSettings(): void
    {
        $settings = new DatabaseSettings();

        // Test that it inherits methods from Settings class
        $this->assertTrue(method_exists($settings, 'getArrayCopy'));
        $this->assertTrue(method_exists($settings, 'offsetExists'));
        $this->assertTrue(method_exists($settings, 'offsetGet'));
        $this->assertTrue(method_exists($settings, 'offsetSet'));
        $this->assertTrue(method_exists($settings, 'offsetUnset'));
    }

    public function testArrayAccess(): void
    {
        $settings = new DatabaseSettings();

        // Test ArrayAccess interface
        $settings['hostname'] = 'testhost';
        $this->assertEquals('testhost', $settings['hostname']);

        $this->assertTrue(isset($settings['hostname']));
        unset($settings['hostname']);
        $this->assertFalse(isset($settings['hostname']));
    }

    public function testSettingsMerging(): void
    {
        $defaultSettings = [
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbName' => 'test_db',
            'persistent' => true
        ];

        $customSettings = [
            'hostname' => 'custom_host',
            'username' => 'custom_user'
        ];

        $settings = new DatabaseSettings($customSettings);

        // Custom settings should override defaults
        $this->assertEquals('custom_host', $settings->hostname);
        $this->assertEquals('custom_user', $settings->username);

        // Defaults should remain for non-overridden settings
        $this->assertEquals('test_db', $settings->dbName);
        $this->assertTrue($settings->persistent);
    }
}