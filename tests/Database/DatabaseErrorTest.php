<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Database;

use PHPUnit\Framework\TestCase;
use Hubbitus\HuPHP\Database\DatabaseError;
use Hubbitus\HuPHP\Database\DatabaseErrorSettings;
use Hubbitus\HuPHP\Debug\HuErrorSettings;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseError
 */
class DatabaseErrorTest extends TestCase
{
    private DatabaseError $error;

    protected function setUp(): void
    {
        $this->error = new DatabaseError(new DatabaseErrorSettings());
    }

    public function testConstructorWithDatabaseErrorSettings(): void
    {
        $settings = new DatabaseErrorSettings();
        $error = new DatabaseError($settings);

        $this->assertInstanceOf(DatabaseError::class, $error);
        $this->assertInstanceOf(DatabaseErrorSettings::class, $error->settings);
    }

    public function testConstructorWithArray(): void
    {
        $settings = [
            'TXT_queryFailed' => 'Custom query failed message',
            'AUTO_DATE' => false,
        ];
        $error = new DatabaseError($settings);

        $this->assertInstanceOf(DatabaseError::class, $error);
        $this->assertEquals('Custom query failed message', $error->TXT_queryFailed);
        $this->assertFalse($error->AUTO_DATE);
    }

    public function testConstructorWithDatabaseError(): void
    {
        $originalSettings = new DatabaseErrorSettings();
        $originalSettings->TXT_queryFailed = 'Original message';
        $originalError = new DatabaseError($originalSettings);

        $newError = new DatabaseError($originalError);

        $this->assertInstanceOf(DatabaseError::class, $newError);
        $this->assertEquals('Original message', $newError->TXT_queryFailed);
    }

    public function testConstructorWithEmptyArray(): void
    {
        $error = new DatabaseError([]);

        $this->assertInstanceOf(DatabaseError::class, $error);
        $this->assertInstanceOf(DatabaseErrorSettings::class, $error->settings);
    }

    public function testDefaultSettings(): void
    {
        $settings = new DatabaseErrorSettings();
        $error = new DatabaseError($settings);

        $this->assertEquals('SQL Query failed', $error->TXT_queryFailed);
        $this->assertEquals('Could not connect to DB', $error->TXT_cantConnect);
        $this->assertEquals('Can not change database', $error->TXT_noDBselected);
        $this->assertTrue($error->AUTO_DATE);
        $this->assertEquals('Y-m-d H:i:s: ', $error->DATE_FORMAT);
        $this->assertEquals('Extra info', $error->EXTRA_HEADER);
    }

    public function testInheritsFromHuError(): void
    {
        $this->assertInstanceOf(HuErrorSettings::class, $this->error->settings);
    }

    public function testSetSetting(): void
    {
        $result = $this->error->setSetting('customField', 'customValue');

        $this->assertSame($this->error, $result);
        $this->assertEquals('customValue', $this->error->customField);
    }

    public function testSetSettingsArray(): void
    {
        $settings = [
            'field1' => 'value1',
            'field2' => 'value2',
        ];

        $this->error->setSettingsArray($settings);

        $this->assertEquals('value1', $this->error->field1);
        $this->assertEquals('value2', $this->error->field2);
    }

    public function testMergeSettingsArray(): void
    {
        $this->error->field1 = 'original';
        $this->error->field2 = 'original2';

        $newSettings = [
            'field1' => 'updated',
            'field3' => 'new',
        ];

        $this->error->mergeSettingsArray($newSettings);

        $this->assertEquals('updated', $this->error->field1);
        $this->assertEquals('original2', $this->error->field2);
        $this->assertEquals('new', $this->error->field3);
    }

    public function testStrForFile(): void
    {
        $this->error->message = 'Test database error';
        $this->error->code = 1045;

        $output = $this->error->strForFile();

        $this->assertIsString($output);
    }

    public function testStrForWeb(): void
    {
        $this->error->message = 'Web database error';
        $output = $this->error->strForWeb();

        $this->assertIsString($output);
    }

    public function testStrForConsole(): void
    {
        $this->error->message = 'Console database error';
        $output = $this->error->strForConsole();

        $this->assertIsString($output);
    }

    public function testToString(): void
    {
        $this->error->message = 'String error';
        $output = (string) $this->error;

        $this->assertIsString($output);
    }

    public function testUpdateDateWithAutoDateEnabled(): void
    {
        $settings = new DatabaseErrorSettings();
        $settings->AUTO_DATE = true;
        $settings->DATE_FORMAT = 'Y-m-d H:i:s';

        $error = new DatabaseError($settings);
        $error->updateDate();

        $this->assertNotEmpty($error->date);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $error->date);
    }

    public function testUpdateDateWithAutoDateDisabled(): void
    {
        $settings = new DatabaseErrorSettings();
        $settings->AUTO_DATE = false;

        $error = new DatabaseError($settings);
        $error->updateDate();

        $this->assertNull($error->date);
    }

    public function testDynamicPropertyAccess(): void
    {
        $this->error->customProperty = 'custom value';

        $this->assertEquals('custom value', $this->error->customProperty);
    }
}
