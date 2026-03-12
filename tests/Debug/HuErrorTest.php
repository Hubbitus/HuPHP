<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;
use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Debug\HuError;

use Hubbitus\HuPHP\Debug\HuErrorSettings;
use Hubbitus\HuPHP\System\OutputType;
use Hubbitus\HuPHP\Vars\OutExtraDataBacktrace;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Debug\HuError
**/
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

	public function testStrForFile(): void {
		$this->error->message = 'Test error message';
		$this->error->code = 500;

		$output = $this->error->strForFile();

		$this->assertIsString($output);
	}

	public function testStrForFileWithFormat(): void {
		$this->error->message = 'Test';
		$output = $this->error->strForFile(['message']);

		$this->assertIsString($output);
	}

	public function testStrForWeb(): void {
		$this->error->message = 'Web error';
		$output = $this->error->strForWeb();

		$this->assertIsString($output);
	}

	public function testStrForWebWithFormat(): void {
		$this->error->message = 'Test';
		$output = $this->error->strForWeb(['message']);

		$this->assertIsString($output);
	}

	public function testStrForConsole(): void {
		$this->error->message = 'Console error';
		$output = $this->error->strForConsole();

		$this->assertIsString($output);
	}

	public function testStrForConsoleWithFormat(): void {
		$this->error->message = 'Test';
		$output = $this->error->strForConsole(['message']);

		$this->assertIsString($output);
	}

	public function testStrForPrint(): void {
		$this->error->message = 'Print error';
		$output = $this->error->strForPrint();

		$this->assertIsString($output);
	}

	public function testStrByOutType(): void {
		$this->error->message = 'Type error';
		$output = $this->error->strByOutType(OutputType::CONSOLE);

		$this->assertIsString($output);
	}

	public function testStrByOutTypeWithFormat(): void {
		$this->error->message = 'Test';
		$output = $this->error->strByOutType(OutputType::FILE, ['message']);

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
		// Default value is OutputType::CONSOLE
		$this->assertEquals(OutputType::CONSOLE, $this->error->_curTypeOut);

		$this->error->strForConsole();
		$this->assertEquals(OutputType::CONSOLE, $this->error->_curTypeOut);
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

	/**
	* Test addExtra method.
	**/
	public function testAddExtra(): void {
		$this->error->addExtra('user_id', 123);
		$this->error->addExtra('request_id', 'abc-456');

		$this->assertEquals(123, $this->error->getExtra('user_id'));
		$this->assertEquals('abc-456', $this->error->getExtra('request_id'));
	}

	/**
	* Test getExtra with non-existent key.
	**/
	public function testGetExtraNonExistent(): void {
		$this->assertNull($this->error->getExtra('nonexistent'));
	}

	/**
	* Test clearExtra method.
	**/
	public function testClearExtra(): void {
		$this->error->addExtra('key1', 'value1');
		$this->error->addExtra('key2', 'value2');

		$this->error->clearExtra();

		$this->assertEmpty($this->error->getAllExtra());
		$this->assertNull($this->error->getExtra('key1'));
	}

	/**
	* Test getAllExtra method.
	**/
	public function testGetAllExtra(): void {
		$this->error->addExtra('key1', 'value1');
		$this->error->addExtra('key2', 'value2');

		$allExtra = $this->error->getAllExtra();

		$this->assertIsArray($allExtra);
		$this->assertCount(2, $allExtra);
		$this->assertEquals('value1', $allExtra['key1']);
		$this->assertEquals('value2', $allExtra['key2']);
	}

	/**
	* Test __toString method.
	**/
	public function testToStringInvocation(): void {
		$this->error->message = 'Test message for __toString';
		$output = $this->error->__toString();

		$this->assertIsString($output);
		$this->assertNotEmpty($output);
	}

	/**
	* Test formatField with empty value.
	**/
	public function testFormatFieldWithEmptyValue(): void {
		$this->error->emptyField = '';
		$formatted = $this->error->formatField('emptyField');

		$this->assertIsString($formatted);
	}

	/**
	* Test formatField with null value.
	**/
	public function testFormatFieldWithNullValue(): void {
		$this->error->nullField = null;
		$formatted = $this->error->formatField('nullField');

		$this->assertIsString($formatted);
	}

	/**
	* Test strForPrint auto-detection.
	**/
	public function testStrForPrintAutoDetection(): void {
		$this->error->message = 'Auto-detect output';
		$output = $this->error->strForPrint();

		$this->assertIsString($output);
		$this->assertNotEmpty($output);
	}

	/**
	* Test strByOutType with WEB type.
	**/
	public function testStrByOutTypeWeb(): void {
		$this->error->message = 'Web output';
		$output = $this->error->strByOutType(OutputType::WEB);

		$this->assertIsString($output);
	}

	/**
	* Test strByOutType with FILE type.
	**/
	public function testStrByOutTypeFile(): void {
		$this->error->message = 'File output';
		$output = $this->error->strByOutType(OutputType::FILE);

		$this->assertIsString($output);
	}

	/**
	* Test updateDate with custom format.
	**/
	public function testUpdateDateCustomFormat(): void {
		$settings = new HuErrorSettings();
		$settings->AUTO_DATE = true;
		$settings->DATE_FORMAT = 'd/m/Y';

		$error = new HuError($settings);
		$error->updateDate();

		$this->assertNotEmpty($error->date);
		$this->assertMatchesRegularExpression('/\d{2}\/\d{2}\/\d{4}/', $error->date);
	}

	/**
	* Test setFromArray with multiple fields.
	**/
	public function testSetFromArrayMultipleFields(): void {
		$settings = [
			'field1' => 'value1',
			'field2' => 'value2',
			'field3' => 'value3',
		];

		$this->error->setFromArray($settings);

		$this->assertEquals('value1', $this->error->field1);
		$this->assertEquals('value2', $this->error->field2);
		$this->assertEquals('value3', $this->error->field3);
	}

	/**
	* Test mergeFromArray with existing fields.
	**/
	public function testMergeFromArrayExistingFields(): void {
		$this->error->existing = 'original';
		$this->error->mergeFromArray(['existing' => 'updated', 'new' => 'value']);

		$this->assertEquals('updated', $this->error->existing);
		$this->assertEquals('value', $this->error->new);
	}

	/**
	* Test DATE property alias.
	**/
	public function testDateAlias(): void {
		$settings = new HuErrorSettings();
		$settings->AUTO_DATE = true;
		$settings->DATE_FORMAT = 'Y-m-d';

		$error = new HuError($settings);
		$error->updateDate();

		// Test 'date' property
		$this->assertNotEmpty($error->date);
	}

	/**
	* Test __get magic method for settings.
	**/
	public function testMagicGetSettings(): void {
		$settings = $this->error->settings;
		$this->assertInstanceOf(HuErrorSettings::class, $settings);
	}

	/**
	* Test __get magic method for date property.
	**/
	public function testMagicGetDate(): void {
		$settings = new HuErrorSettings();
		$settings->AUTO_DATE = true;
		$settings->DATE_FORMAT = 'Y-m-d';

		$error = new HuError($settings);
		$error->updateDate();

		$date = $error->date;
		$this->assertNotEmpty($date);
	}

	/**
	* Test __get magic method for custom property.
	**/
	public function testMagicGetCustomProperty(): void {
		$this->error->customProp = 'custom value';
		$value = $this->error->customProp;

		$this->assertEquals('custom value', $value);
	}

	/**
	* Test __get magic method with DATE uppercase alias.
	**/
	public function testMagicGetDateUppercase(): void {
		$settings = new HuErrorSettings();
		$settings->AUTO_DATE = true;
		$settings->DATE_FORMAT = 'Y-m-d';

		$error = new HuError($settings);
		$error->updateDate();

		// Access via uppercase DATE alias
		$date = $error->DATE;
		$this->assertNotEmpty($date);
		$this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $date);
	}

	/**
	* Test formatField with OutExtraData instance.
	**/
	public function testFormatFieldWithOutExtraData(): void {
		$bt = new Backtrace();
		$extraData = new OutExtraDataBacktrace($bt);
		$this->error->extraField = $extraData;

		$formatted = $this->error->formatField('extraField');

		$this->assertIsString($formatted);
		// OutExtraDataBacktrace returns special string
		$this->assertNotEmpty($formatted);
	}

	/**
	* Test formatField with Backtrace instance.
	**/
	public function testFormatFieldWithBacktrace(): void {
		$bt = new Backtrace();
		$this->error->btField = $bt;

		$formatted = $this->error->formatField('btField');

		$this->assertIsString($formatted);
		$this->assertStringContainsString('Backtrace', $formatted);
	}

	/**
	* Test formatField with array format and suffixes.
	**/
	public function testFormatFieldWithArrayAndSuffixes(): void {
		$this->error->message = 'Test message';

		$formatted = $this->error->formatField(['message', '[', ']']);

		$this->assertIsString($formatted);
		$this->assertStringContainsString('[', $formatted);
		$this->assertStringContainsString(']', $formatted);
	}

	/**
	* Test formatField with associative array (no index 0).
	**/
	public function testFormatFieldWithAssociativeArray(): void {
		$this->error->message = 'Associative test';

		// Array without index 0 - should trigger array_values()
		$formatted = $this->error->formatField(['name' => 'message', 'prefix' => '[', 'suffix' => ']']);

		$this->assertIsString($formatted);
		$this->assertNotEmpty($formatted);
	}
}
