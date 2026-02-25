<?php

/**
 * Test for DatabaseError class.
 */

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseError;
use Hubbitus\HuPHP\Database\DatabaseErrorSettings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseError
 * @covers \Hubbitus\HuPHP\Database\DatabaseErrorSettings
 */
class DatabaseErrorTest extends TestCase {
    public function testClassExtendsHuError(): void {
        $error = new DatabaseError([]);

        $this->assertInstanceOf(DatabaseError::class, $error);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Debug\\HuError', $error);
    }

    public function testConstructorWithDefaultSettings(): void {
        $error = new DatabaseError([]);

        $this->assertInstanceOf(DatabaseError::class, $error);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\DatabaseErrorSettings', $error->_sets);
    }

    public function testConstructorWithArraySettings(): void {
        $customSettings = [
            'TXT_queryFailed' => 'Custom query failed message',
            'TXT_cantConnect' => 'Custom connection failed message',
            'TXT_noDBselected' => 'Custom database selection failed message',
            'DEBUG' => true
        ];

        $error = new DatabaseError($customSettings);

        $this->assertInstanceOf(DatabaseError::class, $error);
        $this->assertEquals('Custom query failed message', $error->TXT_queryFailed);
        $this->assertEquals('Custom connection failed message', $error->TXT_cantConnect);
        $this->assertEquals('Custom database selection failed message', $error->TXT_noDBselected);
        $this->assertTrue($error->DEBUG);
    }

    public function testConstructorWithSettingsObject(): void {
        $settings = new DatabaseErrorSettings();
        $error = new DatabaseError(['custom_setting' => 'value']);

        $this->assertInstanceOf(DatabaseError::class, $error);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\DatabaseErrorSettings', $error->_sets);
    }

    public function testConstructorWithNullSettings(): void {
        $error = new DatabaseError([]);

        $this->assertInstanceOf(DatabaseError::class, $error);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\DatabaseErrorSettings', $error->_sets);
    }

    public function testSettingsInheritance(): void {
        $error = new DatabaseError([]);

        // Test that it inherits methods from HuError
        $this->assertTrue(method_exists($error, 'updateDate'));
        $this->assertTrue(method_exists($error, 'addExtra'));
        $this->assertTrue(method_exists($error, 'getExtra'));
        $this->assertTrue(method_exists($error, 'clearExtra'));
    }

    public function testDefaultErrorMessages(): void {
        $error = new DatabaseError([]);

        $this->assertEquals('SQL Query failed', $error->TXT_queryFailed);
        $this->assertEquals('Could not connect to DB', $error->TXT_cantConnect);
        $this->assertEquals('Can not change database', $error->TXT_noDBselected);
    }

    public function testAutoDateFormat(): void {
        $error = new DatabaseError([]);

        $this->assertTrue($error->AUTO_DATE);
        $this->assertEquals('Y-m-d H:i:s: ', $error->DATE_FORMAT);
    }

    public function testFormatConfigurations(): void {
        $error = new DatabaseError([]);

        // Test that format configurations are inherited from HuErrorSettings
        $this->assertIsArray($error->WEB);
        $this->assertIsArray($error->CONSOLE);
        $this->assertIsArray($error->FILE);
    }

    public function testSettingsModification(): void {
        $error = new DatabaseError([]);

        // Test that we can modify settings
        $error->DEBUG = true;
        $error->TXT_queryFailed = 'Modified query failed message';

        $this->assertTrue($error->DEBUG);
        $this->assertEquals('Modified query failed message', $error->TXT_queryFailed);
    }

    public function testInheritanceOfHuErrorMethods(): void {
        $error = new DatabaseError([]);

        // Test that inherited methods work correctly
        $error->addExtra('test_key', 'test_value');
        $this->assertEquals('test_value', $error->getExtra('test_key'));

        $error->clearExtra();
        $this->assertNull($error->getExtra('test_key'));
    }
}
