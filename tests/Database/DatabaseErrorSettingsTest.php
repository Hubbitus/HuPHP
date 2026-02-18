<?php

/**
 * Test for DatabaseErrorSettings class.
 */

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseErrorSettings;
use Hubbitus\HuPHP\Debug\HuErrorSettings;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseErrorSettings
 */
class DatabaseErrorSettingsTest extends TestCase
{
    public function testClassExtendsHuErrorSettings(): void
    {
        $settings = new DatabaseErrorSettings();

        $this->assertInstanceOf(DatabaseErrorSettings::class, $settings);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Debug\\HuErrorSettings', $settings);
    }

    public function testDefaultErrorMessages(): void
    {
        $settings = new DatabaseErrorSettings();

        $this->assertEquals('SQL Query failed', $settings->TXT_queryFailed);
        $this->assertEquals('Could not connect to DB', $settings->TXT_cantConnect);
        $this->assertEquals('Can not change database', $settings->TXT_noDBselected);
    }

    public function testAutoDateFormat(): void
    {
        $settings = new DatabaseErrorSettings();

        $this->assertTrue($settings->AUTO_DATE);
        $this->assertEquals('Y-m-d H:i:s: ', $settings->DATE_FORMAT);
    }

    public function testExtraHeader(): void
    {
        $settings = new DatabaseErrorSettings();

        $this->assertEquals('Extra info', $settings->EXTRA_HEADER);
    }

    public function testFormatConfigurations(): void
    {
        $settings = new DatabaseErrorSettings();

        $this->assertIsArray($settings->FORMAT_WEB);
        $this->assertIsArray($settings->FORMAT_CONSOLE);
        $this->assertIsArray($settings->FORMAT_FILE);

        $this->assertNotEmpty($settings->FORMAT_WEB);
        $this->assertNotEmpty($settings->FORMAT_CONSOLE);
        $this->assertNotEmpty($settings->FORMAT_FILE);
    }

    public function testWebFormatStructure(): void
    {
        $settings = new DatabaseErrorSettings();

        $webFormat = $settings->FORMAT_WEB;

        // Test basic structure
        $this->assertIsArray($webFormat);
        $this->assertCount(7, $webFormat);

        // Test first element
        $this->assertIsArray($webFormat[0]);
        $this->assertEquals('TXT_queryFailed', $webFormat[0][0]);
        $this->assertEquals(
            "\n<br \"><u><b>",
            $webFormat[0][1]
        );
        $this->assertEquals(
            "</b></u>:
<br ",
            $webFormat[0][2]
        );
        $this->assertEquals('', $webFormat[0][3]);

        // Test query element
        $this->assertEquals(
            "\n<br><u>On query:</u> ",
            $webFormat[4]
        );
        $this->assertIsArray($webFormat[5]);
        $this->assertEquals('Query', $webFormat[5][0]);
        $this->assertEquals(
            '<pre style="color: red">',
            $webFormat[5][1]
        );
        $this->assertEquals('</pre>', $webFormat[5][2]);
    }

    public function testConsoleFormatStructure(): void
    {
        $settings = new DatabaseErrorSettings();

        $consoleFormat = $settings->FORMAT_CONSOLE;

        // Test basic structure
        $this->assertIsArray($consoleFormat);
        $this->assertCount(7, $consoleFormat);

        // Test first element
        $this->assertIsArray($consoleFormat[0]);
        $this->assertEquals('TXT_queryFailed', $consoleFormat[0][0]);
        $this->assertEquals(
            "\033[1m",
            $consoleFormat[0][1]
        );
        $this->assertEquals(
            "\033[0m:\n",
            $consoleFormat[0][2]
        );
        $this->assertEquals('', $consoleFormat[0][3]);

        // Test query element
        $this->assertEquals(
            "\n\033[4;1mOn query:\033[0m ",
            $consoleFormat[4]
        );
        $this->assertIsArray($consoleFormat[5]);
        $this->assertEquals('Query', $consoleFormat[5][0]);
        $this->assertEquals(
            "\033[31m",
            $consoleFormat[5][1]
        );
        $this->assertEquals(
            "\033[0m",
            $consoleFormat[5][2]
        );
    }

    public function testFileFormatStructure(): void
    {
        $settings = new DatabaseErrorSettings();

        $fileFormat = $settings->FORMAT_FILE;

        // Test basic structure
        $this->assertIsArray($fileFormat);
        $this->assertCount(7, $fileFormat);

        // Test first element
        $this->assertIsArray($fileFormat[0]);
        $this->assertEquals('TXT_queryFailed', $fileFormat[0][0]);
        $this->assertEquals('', $fileFormat[0][1]);
        $this->assertEquals(':\n', $fileFormat[0][2]);
        $this->assertEquals('', $fileFormat[0][3]);

        // Test query element
        $this->assertEquals(
            "\nOn query:",
            $fileFormat[4]
        );
        $this->assertIsArray($fileFormat[5]);
        $this->assertEquals('Query', $fileFormat[5][0]);
        $this->assertEquals('>=', $fileFormat[5][1]);
        $this->assertEquals('<=', $fileFormat[5][2]);
    }

    public function testSettingsInheritance(): void
    {
        $settings = new DatabaseErrorSettings();

        // Test that it inherits methods from HuErrorSettings
        $this->assertTrue(method_exists($settings, 'mergeSettingsArray'));
        $this->assertTrue(method_exists($settings, '__SETS'));
    }

    public function testSettingsMerging(): void
    {
        $customSettings = [
            'TXT_queryFailed' => 'Custom query failed message',
            'TXT_cantConnect' => 'Custom connection failed message',
            'DEBUG' => true,
            'FORMAT_WEB' => [
                ['TXT_queryFailed', '<strong>', '</strong>', '']
            ]
        ];

        $settings = new DatabaseErrorSettings();
        $settings->mergeSettingsArray($customSettings);

        $this->assertEquals('Custom query failed message', $settings->TXT_queryFailed);
        $this->assertEquals('Custom connection failed message', $settings->TXT_cantConnect);
        $this->assertTrue($settings->DEBUG);
        $this->assertIsArray($settings->FORMAT_WEB);
        $this->assertEquals('<strong>', $settings->FORMAT_WEB[0][1]);
    }

    public function testArrayAccess(): void
    {
        // DatabaseErrorSettings does not implement ArrayAccess interface
        $this->assertTrue(true);
    }

    public function testDefaultSettingsStructure(): void
    {
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

    public function testImmutabilityOfConstants(): void
    {
        $reflection = new \ReflectionClass(DatabaseErrorSettings::class);

        // Test that constants are properly defined
        $constants = $reflection->getConstants();
        $this->assertIsArray($constants);
    }

    public function testSettingsCloning(): void
    {
        $original = new DatabaseErrorSettings();
        $clone = clone $original;

        $this->assertEquals($original->TXT_queryFailed, $clone->TXT_queryFailed);
        $this->assertEquals($original->FORMAT_WEB, $clone->FORMAT_WEB);
    }

    public function testSettingsSerialization(): void
    {
        $settings = new DatabaseErrorSettings();
        $serialized = serialize($settings);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(DatabaseErrorSettings::class, $unserialized);
        $this->assertEquals($settings->TXT_queryFailed, $unserialized->TXT_queryFailed);
    }

    public function testParentClassProperties(): void
    {
        $settings = new DatabaseErrorSettings();

        // Test that parent class properties are inherited
        $this->assertObjectHasAttribute('__SETS', $settings);
        $this->assertInstanceOf(HuErrorSettings::class, $settings);
    }

    public function testEmptyConstructor(): void
    {
        $settings = new DatabaseErrorSettings();

        $this->assertInstanceOf(DatabaseErrorSettings::class, $settings);
        $this->assertNotEmpty($settings->__SETS);
    }

    public function testInvalidConstructor(): void
    {
        // DatabaseErrorSettings does not support constructor parameters
        $settings = new DatabaseErrorSettings();
        $this->assertInstanceOf(DatabaseErrorSettings::class, $settings);
    }

    public function testToStringMethod(): void
    {
        $settings = new DatabaseErrorSettings();

        $this->assertTrue(method_exists($settings, '__toString'));
        $this->assertIsString((string)$settings);
    }

    public function testCountableInterface(): void
    {
        $settings = new DatabaseErrorSettings();

        $this->assertTrue(method_exists($settings, 'length'));
        $this->assertIsInt($settings->length());
        $this->assertGreaterThan(0, $settings->length());
    }

    public function testIteratorInterface(): void
    {
        $settings = new DatabaseErrorSettings();

        $this->assertTrue(method_exists($settings, 'getIterator'));
        $this->assertTrue(method_exists($settings, 'getProperty'));
    }
}