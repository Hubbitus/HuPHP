<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use PHPUnit\Framework\TestCase;
use Hubbitus\HuPHP\Debug\HuError;
use Hubbitus\HuPHP\Debug\HuErrorSettings;
use Hubbitus\HuPHP\System\OS;

/**
* @covers \Hubbitus\HuPHP\Debug\HuError
*/
class HuErrorTest extends TestCase {
	private HuError $error;

	protected function setUp(): void {
		$this->error = new HuError(new HuErrorSettings());
	}

	public function testConstructor(): void {
		$error = new HuError();
		$this->assertInstanceOf(HuError::class, $error);
	}

	public function testConstructorWithSettings(): void {
		$settings = new HuErrorSettings();
		$settings->AUTO_DATE = false;
		$error = new HuError($settings);

		$this->assertInstanceOf(HuError::class, $error);
		$this->assertFalse($error->settings->AUTO_DATE);
	}

	public function testGetSettings(): void {
		$this->assertInstanceOf(HuErrorSettings::class, $this->error->settings);
	}

	public function testSetSetting(): void {
		$result = $this->error->setSetting('testField', 'testValue');

		$this->assertSame($this->error, $result);
		$this->assertEquals('testValue', $this->error->testField);
	}

	public function testSetSettingsArray(): void {
		$settings = [
			'field1' => 'value1',
			'field2' => 'value2',
		];

		$this->error->setSettingsArray($settings);

		$this->assertEquals('value1', $this->error->field1);
		$this->assertEquals('value2', $this->error->field2);
	}

	public function testMergeSettingsArray(): void {
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

	public function testSetFromArray(): void {
		$settings = ['test' => 'value'];
		$this->error->setFromArray($settings);

		$this->assertEquals('value', $this->error->test);
	}

	public function testMergeFromArray(): void {
		$this->error->existing = 'value';
		$this->error->mergeFromArray(['new' => 'data']);

		$this->assertEquals('value', $this->error->existing);
		$this->assertEquals('data', $this->error->new);
	}

	public function testStrToFile(): void {
		$this->error->message = 'Test error message';
		$this->error->code = 500;

		$output = $this->error->strToFile();

		$this->assertIsString($output);
	}

	public function testStrToFileWithFormat(): void {
		$this->error->message = 'Test';
		$output = $this->error->strToFile(['message']);

		$this->assertIsString($output);
	}

	public function testStrToWeb(): void {
		$this->error->message = 'Web error';
		$output = $this->error->strToWeb();

		$this->assertIsString($output);
	}

	public function testStrToWebWithFormat(): void {
		$this->error->message = 'Test';
		$output = $this->error->strToWeb(['message']);

		$this->assertIsString($output);
	}

	public function testStrToConsole(): void {
		$this->error->message = 'Console error';
		$output = $this->error->strToConsole();

		$this->assertIsString($output);
	}

	public function testStrToConsoleWithFormat(): void {
		$this->error->message = 'Test';
		$output = $this->error->strToConsole(['message']);

		$this->assertIsString($output);
	}

	public function testStrToPrint(): void {
		$this->error->message = 'Print error';
		$output = $this->error->strToPrint();

		$this->assertIsString($output);
	}

	public function testStrByOutType(): void {
		$this->error->message = 'Type error';
		$output = $this->error->strByOutType(OS::OUT_TYPE_CONSOLE);

		$this->assertIsString($output);
	}

	public function testStrByOutTypeWithFormat(): void {
		$this->error->message = 'Test';
		$output = $this->error->strByOutType(OS::OUT_TYPE_FILE, ['message']);

		$this->assertIsString($output);
	}

	public function testToString(): void {
		$this->error->message = 'String error';
		$output = (string)$this->error;

		$this->assertIsString($output);
	}

	public function testUpdateDateWithAutoDateEnabled(): void {
		$settings = new HuErrorSettings();
		$settings->AUTO_DATE = true;
		$settings->DATE_FORMAT = 'Y-m-d H:i:s';

		$error = new HuError($settings);
		$error->updateDate();

		$this->assertNotEmpty($error->date);
		$this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $error->date);
	}

	public function testUpdateDateWithAutoDateDisabled(): void {
		$settings = new HuErrorSettings();
		$settings->AUTO_DATE = false;

		$error = new HuError($settings);
		$error->updateDate();

		$this->assertNull($error->date);
	}

	public function testUpdateDateOnSetSetting(): void {
		$settings = new HuErrorSettings();
		$settings->AUTO_DATE = true;
		$settings->DATE_FORMAT = 'Y-m-d';

		$error = new HuError($settings);
		$error->setSetting('message', 'test');

		$this->assertNotEmpty($error->date);
	}

	public function testUpdateDateOnMergeSettings(): void {
		$settings = new HuErrorSettings();
		$settings->AUTO_DATE = true;
		$settings->DATE_FORMAT = 'Y';

		$error = new HuError($settings);
		$error->mergeSettingsArray(['new' => 'field']);

		$this->assertNotEmpty($error->date);
	}

	public function testDynamicPropertyAccess(): void {
		$this->error->customProperty = 'custom value';

		$this->assertEquals('custom value', $this->error->customProperty);
	}

	public function testCurTypeOutProperty(): void {
		$this->assertEquals(OS::OUT_TYPE_BROWSER, $this->error->_curTypeOut);

		$this->error->strToConsole();
		$this->assertEquals(OS::OUT_TYPE_CONSOLE, $this->error->_curTypeOut);
	}

	public function testFormatFieldWithString(): void {
		$this->error->message = 'Test message';

		$formatted = $this->error->formatField('message');

		$this->assertIsString($formatted);
		$this->assertStringContainsString('Test message', $formatted);
	}

	public function testFormatFieldWithArray(): void {
		$this->error->message = 'Array test';

		$formatted = $this->error->formatField(['message', '[', ']']);

		$this->assertIsString($formatted);
	}

	public function testSetSettingReturnsReference(): void {
		$result = $this->error->setSetting('test', 'value');

		$this->assertSame($this->error, $result);
	}

	public function testMultipleSetSettings(): void {
		$this->error->setSetting('field1', 'value1');
		$this->error->setSetting('field2', 'value2');
		$this->error->setSetting('field3', 'value3');

		$this->assertEquals('value1', $this->error->field1);
		$this->assertEquals('value2', $this->error->field2);
		$this->assertEquals('value3', $this->error->field3);
	}
}
