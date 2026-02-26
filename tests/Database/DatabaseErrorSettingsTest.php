<?php

/**
 * Test for DatabaseErrorSettings class.
 */

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseErrorSettings;
use Hubbitus\HuPHP\Debug\HuErrorSettings;
use Hubbitus\HuPHP\System\OutputType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseErrorSettings
 */
class DatabaseErrorSettingsTest extends TestCase {
    public function testClassExtendsHuErrorSettings(): void {
        $settings = new DatabaseErrorSettings();

        $this->assertInstanceOf(DatabaseErrorSettings::class, $settings);
        $this->assertInstanceOf(HuErrorSettings::class, $settings);
    }

    public function testDefaultErrorMessages(): void {
        $settings = new DatabaseErrorSettings();

        $this->assertEquals('SQL Query failed', $settings->TXT_queryFailed);
        $this->assertEquals('Could not connect to DB', $settings->TXT_cantConnect);
        $this->assertEquals('Can not change database', $settings->TXT_noDBselected);
    }

    public function testAutoDateFormat(): void {
        $settings = new DatabaseErrorSettings();

        $this->assertTrue($settings->AUTO_DATE);
        $this->assertEquals('Y-m-d H:i:s: ', $settings->DATE_FORMAT);
    }

    public function testExtraHeader(): void {
        $settings = new DatabaseErrorSettings();

        $this->assertEquals('Extra info', $settings->EXTRA_HEADER);
    }

    public function testFormatConfigurations(): void {
        $settings = new DatabaseErrorSettings();

        $this->assertIsArray($settings->WEB);
        $this->assertIsArray($settings->CONSOLE);
        $this->assertIsArray($settings->FILE);

        $this->assertNotEmpty($settings->WEB);
        $this->assertNotEmpty($settings->CONSOLE);
        $this->assertNotEmpty($settings->FILE);
    }

    public function testWebFormatStructure(): void {
        $settings = new DatabaseErrorSettings();

        $webFormat = $settings->WEB;

        // Test basic structure
        $this->assertIsArray($webFormat);
        $this->assertCount(7, $webFormat);

        // Test first element
        $this->assertIsArray($webFormat[0]);
        $this->assertEquals('TXT_queryFailed', $webFormat[0][0]);

        // Test query element
        $this->assertEquals(
            "\n<br><u>On query:</u> ",
            $webFormat[4]
        );
        $this->assertIsArray($webFormat[5]);
        $this->assertEquals('Query', $webFormat[5][0]);
    }

    public function testConsoleFormatStructure(): void {
        $settings = new DatabaseErrorSettings();

        $consoleFormat = $settings->CONSOLE;

        // Test basic structure
        $this->assertIsArray($consoleFormat);
        $this->assertCount(7, $consoleFormat);

        // Test first element
        $this->assertIsArray($consoleFormat[0]);
        $this->assertEquals('TXT_queryFailed', $consoleFormat[0][0]);

        // Test query element
        $this->assertEquals(
            "\n\033[4;1mOn query:\033[0m ",
            $consoleFormat[4]
        );
        $this->assertIsArray($consoleFormat[5]);
        $this->assertEquals('Query', $consoleFormat[5][0]);
    }

    public function testFileFormatStructure(): void {
        $settings = new DatabaseErrorSettings();

        $fileFormat = $settings->FILE;

        // Test basic structure
        $this->assertIsArray($fileFormat);
        $this->assertCount(7, $fileFormat);

        // Test first element
        $this->assertIsArray($fileFormat[0]);
        $this->assertEquals('TXT_queryFailed', $fileFormat[0][0]);

        // Test query element
        $this->assertEquals(
            "\nOn query:",
            $fileFormat[4]
        );
        $this->assertIsArray($fileFormat[5]);
        $this->assertEquals('Query', $fileFormat[5][0]);
    }

    public function testSettingsInheritance(): void {
        $settings = new DatabaseErrorSettings();

        // Test that it inherits methods from HuErrorSettings
        $this->assertTrue(method_exists($settings, 'mergeSettingsArray'));
        $this->assertTrue(method_exists($settings, 'getProperty'));
    }

    public function testSettingsMerging(): void {
        $customSettings = [
            'TXT_queryFailed' => 'Custom query failed message',
            'TXT_cantConnect' => 'Custom connection failed message',
            'DEBUG' => true,
        ];

        $settings = new DatabaseErrorSettings();
        $settings->mergeSettingsArray($customSettings);

        $this->assertEquals('Custom query failed message', $settings->TXT_queryFailed);
        $this->assertEquals('Custom connection failed message', $settings->TXT_cantConnect);
        $this->assertTrue($settings->DEBUG);
    }

    public function testDefaultSettingsStructure(): void {
        $settings = new DatabaseErrorSettings();

        // Test that all expected default settings are present
        $expectedDefaults = [
            'TXT_queryFailed' => 'SQL Query failed',
            'TXT_cantConnect' => 'Could not connect to DB',
            'TXT_noDBselected' => 'Can not change database',
            'AUTO_DATE' => true,
            'DATE_FORMAT' => 'Y-m-d H:i:s: ',
            'EXTRA_HEADER' => 'Extra info'
        ];

        foreach ($expectedDefaults as $key => $value) {
            $this->assertEquals($value, $settings->$key);
        }
    }

    public function testSettingsCloning(): void {
        $original = new DatabaseErrorSettings();
        $clone = clone $original;

        $this->assertEquals($original->TXT_queryFailed, $clone->TXT_queryFailed);
        $this->assertEquals($original->WEB, $clone->WEB);
    }

    public function testSettingsSerialization(): void {
        $settings = new DatabaseErrorSettings();
        $serialized = serialize($settings);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(DatabaseErrorSettings::class, $unserialized);
        $this->assertEquals($settings->TXT_queryFailed, $unserialized->TXT_queryFailed);
    }

    public function testEmptyConstructor(): void {
        $settings = new DatabaseErrorSettings();

        $this->assertInstanceOf(DatabaseErrorSettings::class, $settings);
        // Check that __SETS has data via getProperty
        $this->assertNotEmpty($settings->getProperty('TXT_queryFailed'));
    }

    public function testGetPropertyMethod(): void {
        $settings = new DatabaseErrorSettings();

        $this->assertEquals('SQL Query failed', $settings->getProperty('TXT_queryFailed'));
    }

    public function testSetSettingMethod(): void {
        $settings = new DatabaseErrorSettings();
        $settings->setSetting('CUSTOM_KEY', 'custom_value');

        $this->assertEquals('custom_value', $settings->CUSTOM_KEY);
    }
}
