<?php

/**
 * Test for DatabaseSettingsMSSQL class.
 */

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseSettingsMSSQL;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseSettingsMSSQL
 */
class DatabaseSettingsMSSQLTest extends TestCase
{
    public function testClassExtendsDatabaseSettings(): void {
        $settings = new DatabaseSettingsMSSQL();

        $this->assertInstanceOf(DatabaseSettingsMSSQL::class, $settings);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\DatabaseSettings', $settings);
    }

    public function testConstructorWithDefaultSettings(): void {
        $settings = new DatabaseSettingsMSSQL();

        $this->assertInstanceOf(DatabaseSettingsMSSQL::class, $settings);
        $this->assertEquals('UTF-8', $settings->CHARSET_RECODE['TO']);
        $this->assertEquals('CP1251', $settings->CHARSET_RECODE['FROM']);
        $this->assertFalse($settings->DEBUG);
    }

    public function testConstructorWithCustomSettings(): void {
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

        $settings = new DatabaseSettingsMSSQL($customSettings);

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

    public function testGettersAndSetters(): void {
        $settings = new DatabaseSettingsMSSQL();

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

    public function testDefaultSettingsStructure(): void {
        $settings = new DatabaseSettingsMSSQL();

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

    public function testInheritanceFromDatabaseSettings(): void {
        $settings = new DatabaseSettingsMSSQL();

        // Test that it inherits methods from DatabaseSettings
        $this->assertTrue(method_exists($settings, 'getArrayCopy'));
        $this->assertTrue(method_exists($settings, 'offsetExists'));
        $this->assertTrue(method_exists($settings, 'offsetGet'));
        $this->assertTrue(method_exists($settings, 'offsetSet'));
        $this->assertTrue(method_exists($settings, 'offsetUnset'));
    }

    public function testArrayAccess(): void {
        $settings = new DatabaseSettingsMSSQL();

        // Test ArrayAccess interface
        $settings['hostname'] = 'testhost';
        $this->assertEquals('testhost', $settings['hostname']);

        $this->assertTrue(isset($settings['hostname']));
        unset($settings['hostname']);
        $this->assertFalse(isset($settings['hostname']));
    }

    public function testSettingsMerging(): void {
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

        $settings = new DatabaseSettingsMSSQL($customSettings);

        // Custom settings should override defaults
        $this->assertEquals('custom_host', $settings->hostname);
        $this->assertEquals('custom_user', $settings->username);

        // Defaults should remain for non-overridden settings
        $this->assertEquals('test_db', $settings->dbName);
        $this->assertTrue($settings->persistent);
    }

    public function testClassConstants(): void {
        $reflection = new \ReflectionClass(DatabaseSettingsMSSQL::class);

        // Test that the class has the INT_STR_LENGTH constant
        $this->assertTrue($reflection->hasConstant('INT_STR_LENGTH'));
        $this->assertEquals(10, $reflection->getConstant('INT_STR_LENGTH'));

        // Test that the constant is of correct type
        $this->assertIsInt($reflection->getConstant('INT_STR_LENGTH'));
    }

    public function testConstantUsage(): void {
        $settings = new DatabaseSettingsMSSQL();

        // Test that the constant can be accessed statically
        $this->assertEquals(10, DatabaseSettingsMSSQL::INT_STR_LENGTH);

        // Test that the constant is not modifiable
        $reflection = new \ReflectionClass(DatabaseSettingsMSSQL::class);
        $constant = $reflection->getReflectionConstant('INT_STR_LENGTH');
        $this->assertTrue($constant->isPublic());
        $this->assertTrue($constant->isFinal());
    }

    public function testConstructorWithArrayAndConstant(): void {
        $customSettings = [
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => 'pass',
            'dbName' => 'test_db',
            'INT_STR_LENGTH' => 15 // This should be ignored as it's a constant
        ];

        $settings = new DatabaseSettingsMSSQL($customSettings);

        // The constant should not be set as a regular property
        $this->assertFalse(isset($settings->INT_STR_LENGTH));
        $this->assertEquals(10, DatabaseSettingsMSSQL::INT_STR_LENGTH);
    }

    public function testParentClassConstants(): void {
        $reflection = new \ReflectionClass(DatabaseSettingsMSSQL::class);

        // Test that parent class constants are inherited
        $parentReflection = $reflection->getParentClass();
        $this->assertNotNull($parentReflection);
        $this->assertInstanceOf('ReflectionClass', $parentReflection);
    }

    public function testEmptyConstructor(): void {
        $settings = new DatabaseSettingsMSSQL();

        $this->assertInstanceOf(DatabaseSettingsMSSQL::class, $settings);
        $this->assertEmpty($settings->getArrayCopy());
    }

    public function testNullConstructor(): void {
        $settings = new DatabaseSettingsMSSQL(null);

        $this->assertInstanceOf(DatabaseSettingsMSSQL::class, $settings);
        $this->assertEmpty($settings->getArrayCopy());
    }

    public function testInvalidConstructor(): void {
        $settings = new DatabaseSettingsMSSQL('invalid');

        $this->assertInstanceOf(DatabaseSettingsMSSQL::class, $settings);
        $this->assertEmpty($settings->getArrayCopy());
    }
}