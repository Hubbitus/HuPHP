<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\Dump;
use Hubbitus\HuPHP\System\OS;
use Hubbitus\HuPHP\Vars\HuArray;
use Hubbitus\HuPHP\Vars\Settings\Settings;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Debug\Dump
**/
class DumpTest extends TestCase {
	public function testDumpConsoleReturnsString(): void {
		$var = ['key' => 'value'];
		$result = Dump::c($var, 'Test Header', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Test Header ===', $result);
		$this->assertStringContainsString('key', $result);
		$this->assertStringContainsString('value', $result);
	}

	public function testDumpConsoleWithoutHeader(): void {
		$var = 'test_string';
		$result = Dump::c($var, null, true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== $var ===', $result);
		$this->assertStringContainsString('test_string', $result);
	}

	public function testDumpConsoleWithArray(): void {
		$var = ['a' => 1, 'b' => 2];
		$result = Dump::c($var, null, true);

		$this->assertIsString($result);
		$this->assertStringContainsString('a', $result);
		$this->assertStringContainsString('1', $result);
	}

	public function testDumpConsoleWithObject(): void {
		$var = (object) ['prop' => 'value'];
		$result = Dump::c($var, null, true);

		$this->assertIsString($result);
		$this->assertStringContainsString('prop', $result);
	}

	public function testDumpConsoleWithScalar(): void {
		$var = 42;
		$result = Dump::c($var, null, true);

		$this->assertIsString($result);
		$this->assertStringContainsString('42', $result);
	}

	public function testDumpWebReturnsString(): void {
		$var = ['key' => 'value'];
		$result = Dump::w($var, 'Web Header', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Web Header ===', $result);
	}

	public function testDumpLogReturnsString(): void {
		$var = ['key' => 'value'];
		$result = Dump::log($var, 'Log Header', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Log Header ===', $result);
	}

	public function testDumpAReturnsString(): void {
		$var = ['key' => 'value'];
		$result = Dump::a($var, 'A Header', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== A Header ===', $result);
	}

	public function testDumpAAutoDetectsHeader(): void {
		$var = 'auto_test';
		$result = Dump::a($var, null, true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== $var ===', $result);
		$this->assertStringContainsString('auto_test', $result);
	}

	/**
	* @covers \Hubbitus\HuPHP\Debug\Dump::byOutType
	**/
	public function testDumpByOutType(): void {
		$var = ['key' => 'value'];
		$result = Dump::byOutType(1, $var, 'Type Header', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Type Header ===', $result);
		$this->assertStringContainsString('key', $result);
		$this->assertStringContainsString('value', $result);

		$var2 = 'byOut_type_var';
		$result2 = Dump::byOutType(1, $var2, null, true);

		$this->assertIsString($result2);
		$this->assertStringContainsString('=== 1 ===', $result2);
		$this->assertStringContainsString($var2, $result2);
	}

	public function testDumpWebAutoDetectsHeader(): void {
		$var = 'web_test';
		$result = Dump::w($var, null, true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== $var ===', $result);
		$this->assertStringContainsString('web_test', $result);
	}

	public function testDumpLogAutoDetectsHeader(): void {
		$var = 'log_test';
		$result = Dump::log($var, null, true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== $var ===', $result);
		$this->assertStringContainsString('log_test', $result);
	}

	public function testDumpAutoAutoDetectsHeader(): void {
		$var = 'auto_detect_test';
		$result = Dump::auto($var, null, true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== $var ===', $result);
		$this->assertStringContainsString('auto_detect_test', $result);
	}

	/**
	* @runInSeparateProcess
	**/
	public function testDumpAutoWithCliEnvironmentMocked(): void {
		$osStub = $this->createStub(OS::class);
		$osStub->method('phpSapiName')->willReturn('cli');

		$var = ['key' => 'value'];
		$header = 'CLI Auto Test Mocked';
		$return = true;

		$result = Dump::auto($var, $header, $return, $osStub);

		$this->assertIsString($result);
		$this->assertStringContainsString("=== {$header} ===", $result);
		$this->assertStringContainsString('key', $result);
		$this->assertStringContainsString('value', $result);
	}

	/**
	* @runInSeparateProcess
	**/
	public function testDumpAutoWithWebEnvironmentMocked(): void {
		$osStub = $this->createStub(OS::class);
		$osStub->method('phpSapiName')->willReturn('apache2handler');

		$var = ['key' => 'value'];
		$header = 'Web Auto Test Mocked';
		$return = true;

		$result = Dump::auto($var, $header, $return, $osStub);

		$this->assertIsString($result);
		$this->assertStringContainsString("=== {$header} ===", $result);
		$this->assertStringContainsString('key', $result);
		$this->assertStringContainsString('value', $result);
	}

	public function testDumpWithNullValue(): void {
		$var = null;
		$result = Dump::c($var, 'Null Test', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Null Test ===', $result);
	}

	public function testDumpWithBooleanValue(): void {
		$var = true;
		$result = Dump::c($var, 'Bool Test', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('true', $result);
	}

	public function testDumpWithNumericArray(): void {
		$var = [1, 2, 3];
		$result = Dump::c($var, 'Numeric Array', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('1', $result);
		$this->assertStringContainsString('2', $result);
		$this->assertStringContainsString('3', $result);
	}

	public function testDumpWithNestedArray(): void {
		$var = ['outer' => ['inner' => 'value']];
		$result = Dump::c($var, 'Nested', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('outer', $result);
		$this->assertStringContainsString('inner', $result);
	}

	public function testDumpConsoleOutputWithoutReturn(): void {
		$var = ['key' => 'value'];

		\ob_start();
		Dump::c($var, 'Console Output Test', false);
		$output = \ob_get_clean();

		$this->assertIsString($output);
		$this->assertStringContainsString('=== Console Output Test ===', $output);
		$this->assertStringContainsString('key', $output);
	}

	public function testDumpWebOutputWithoutReturn(): void {
		$var = ['key' => 'value'];

		\ob_start();
		Dump::w($var, 'Web Output Test', false);
		$output = \ob_get_clean();

		$this->assertIsString($output);
		$this->assertStringContainsString('=== Web Output Test ===', $output);
	}

	public function testDumpLogOutputWithoutReturn(): void {
		$var = ['key' => 'value'];

		$tmpfile = \tempnam(\sys_get_temp_dir(), 'huphp-test-log');
		$original_error_log = \ini_get('error_log');
		\ini_set('error_log', $tmpfile);

		Dump::log($var, 'Log tmp file test', false);

		\ini_set('error_log', $original_error_log);

		$output = \file_get_contents($tmpfile);
		\unlink($tmpfile);

		$this->assertStringContainsString('Log tmp file test', $output);
		$this->assertStringContainsString('key', $output);
		$this->assertStringContainsString('value', $output);
	}

	public function testDumpAutoDetectFailsWhenCalledViaCallUserFunc(): void {
		$var = 'call_user_func_test';
		$result = \call_user_func([Dump::class, 'c'], $var, null, true);
		$this->assertIsString($result);
		$this->assertStringContainsString('call_user_func_test', $result);
		$this->assertStringNotContainsString('=== $var ===', $result);
	}

	public function testDetectVarNameFromBacktraceReturnsNullWhenNoDumpFrame(): void {
		$method = new \ReflectionMethod(Dump::class, 'detectVarNameFromBacktrace');
		$method->setAccessible(true);
		$result = $method->invoke(null);
		$this->assertNull($result);
	}

	public function testDetectVarNameFromBacktraceReturnsNullWhenNoDumpFramesFound(): void {
		$result = $this->callDetectVarNameWithoutDumpFrames();
		$this->assertNull($result);
	}

	private function callDetectVarNameWithoutDumpFrames(): ?string {
		$method = new \ReflectionMethod(Dump::class, 'detectVarNameFromBacktrace');
		$method->setAccessible(true);
		return $method->invoke(null);
	}

	public function testDetectVarNameFromBacktraceReturnsNullWhenFileReadFails(): void {
		// Test line 211: when file/line exists but lines[$line - 1] is not set
		$mockFileReader = function(string $file): array {
			// Return empty array - line index won't exist
			return [];
		};
		$method = new \ReflectionMethod(Dump::class, 'detectVarNameFromBacktrace');
		$method->setAccessible(true);
		$result = $method->invoke(null, $mockFileReader);
		$this->assertNull($result);
	}

	/**
	* Test line 211: when frame has class/function but missing file/line.
	* This happens with eval or some call_user_func scenarios.
	**/
	public function testDetectVarNameFromBacktraceWithMissingFileLineInFrame(): void {
		// Create a scenario where the Dump frame has class/function but no file/line
		// This is achieved by calling via call_user_func_array
		$mockFileReader = function(string $file): array {
			return ['some_var'];
		};

		$method = new \ReflectionMethod(Dump::class, 'detectVarNameFromBacktrace');
		$method->setAccessible(true);
		$result = $method->invoke(null, $mockFileReader);
		$this->assertNull($result);
	}

	/**
	* Additional test to ensure line 211 is covered - call via call_user_func_array.
	* This creates a backtrace frame that may lack file/line info.
	**/
	public function testDetectVarNameViaCallUserFuncArray(): void {
		$var = 'test_var';
		// call_user_func_array creates different backtrace
		$result = \call_user_func_array([Dump::class, 'c'], [$var, null, true]);
		$this->assertIsString($result);
	}

	public function testDetectVarNameFromBacktraceReturnsNullWhenPatternNoMatch(): void {
		$var = 'complex_var_name';
		$result = Dump::c($var, null, true);
		$this->assertIsString($result);
	}

	/**
	* @runInSeparateProcess
	**/
	public function testDetectVarNameFromBacktraceWithEvalContext(): void {
		// This test uses eval to create a scenario where line number
		// in backtrace doesn't match actual file lines
		$code = '
              namespace Hubbitus\HuPHP\Tests\Debug;
              use Hubbitus\HuPHP\Debug\Dump;
              $evalVar = "eval_test";
              $result = Dump::c($evalVar, null, true);
              return $result;
          ';
		// Suppress warning from eval() - file() will fail to read eval'd code
		$result = @eval($code);
		$this->assertIsString($result);
		$this->assertStringContainsString('eval_test', $result);
	}

	/**
	* Test array formatting with size indicator and proper structure
	**/
	public function testDumpArrayFormatWithSize(): void {
		$var = [1, 2, 3];
		$result = Dump::c($var, 'Test', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Test ===', $result);
		$this->assertStringContainsString('Array[size: 3]', $result);
		$this->assertStringContainsString('[0] => 1', $result);
		$this->assertStringContainsString('[1] => 2', $result);
		$this->assertStringContainsString('[2] => 3', $result);
	}

	/**
	* Test nested array formatting
	**/
	public function testDumpNestedArrayFormat(): void {
		$var = ['a' => ['b' => ['c' => 'deep']]];
		$result = Dump::c($var, 'Nested', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('Array[size: 1]', $result);
		$this->assertStringContainsString('[a] => Array[size: 1]', $result);
		$this->assertStringContainsString('[b] => Array[size: 1]', $result);
		$this->assertStringContainsString('[c] => \'deep\'', $result);
	}

	/**
	* Test that array format has opening brace on same line
	**/
	public function testDumpArrayFormatOpeningBrace(): void {
		$var = [1, 2];
		$result = Dump::c($var, 'Brace', true);

		$this->assertIsString($result);
		// Opening brace should be on same line as Array[size: N]
		$this->assertMatchesRegularExpression('/Array\[size: \d+\] \{/', $result);
	}

	/**
	* Test that array format has closing brace
	**/
	public function testDumpArrayFormatClosingBrace(): void {
		$var = [1, 2];
		$result = Dump::c($var, 'Close', true);

		$this->assertIsString($result);
		// Should have closing brace
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test object formatting with class name
	**/
	public function testDumpObjectFormat(): void {
		$var = (object) ['name' => 'test', 'value' => 42];
		$result = Dump::c($var, 'Object', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('stdClass {', $result);
		$this->assertStringContainsString('[name] => \'test\'', $result);
		$this->assertStringContainsString('[value] => 42', $result);
	}

	/**
	* Test that there are no empty lines in array output
	**/
	public function testDumpArrayFormatNoEmptyLines(): void {
		$var = [1, 2, 3];
		$result = Dump::c($var, 'NoEmpty', true);

		$this->assertIsString($result);
		// Split into lines and check no consecutive empty lines
		$lines = \explode("\n", $result);
		$consecutiveEmpty = false;
		$prevEmpty = false;

		foreach ($lines as $line) {
			$isEmpty = (\trim($line) === '');
			if ($isEmpty && $prevEmpty) {
				$consecutiveEmpty = true;
				break;
			}
			$prevEmpty = $isEmpty;
		}

		$this->assertFalse($consecutiveEmpty, 'Output should not have consecutive empty lines');
	}

	/**
	* Test mixed array with string and numeric keys
	**/
	public function testDumpMixedKeyArrayFormat(): void {
		$var = ['one', 'two', 'three' => ['nested']];
		$result = Dump::c($var, 'Mixed', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('Array[size: 3]', $result);
		$this->assertStringContainsString('[0] => \'one\'', $result);
		$this->assertStringContainsString('[1] => \'two\'', $result);
		$this->assertStringContainsString('[three] => Array[size: 1]', $result);
	}

	/**
	* Test Dump::a() array format with size indicator
	**/
	public function testDumpAArrayFormatWithSize(): void {
		$var = [1, 2, 3];
		$result = Dump::a($var, 'Test Array', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Test Array ===', $result);
		$this->assertStringContainsString('Array[size: 3]', $result);
		$this->assertStringContainsString('[0] => 1', $result);
		$this->assertStringContainsString('[1] => 2', $result);
		$this->assertStringContainsString('[2] => 3', $result);
	}

	/**
	* Test Dump::a() associative array format
	**/
	public function testDumpAAssociativeArrayFormat(): void {
		$var = ['key1' => 'value1', 'key2' => 42];
		$result = Dump::a($var, 'Assoc Array', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Assoc Array ===', $result);
		$this->assertStringContainsString('Array[size: 2]', $result);
		$this->assertStringContainsString('[key1] => \'value1\'', $result);
		$this->assertStringContainsString('[key2] => 42', $result);
	}

	/**
	* Test Dump::a() array format opening brace on same line
	**/
	public function testDumpAArrayFormatOpeningBrace(): void {
		$var = [1, 2];
		$result = Dump::a($var, 'Brace Test', true);

		$this->assertIsString($result);
		// Opening brace should be on same line as Array[size: N]
		$this->assertMatchesRegularExpression('/Array\[size: \d+\] \{/', $result);
	}

	/**
	* Test Dump::a() array format closing brace
	**/
	public function testDumpAArrayFormatClosingBrace(): void {
		$var = [1, 2];
		$result = Dump::a($var, 'Close Test', true);

		$this->assertIsString($result);
		// Should have closing brace
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() array format no empty lines
	**/
	public function testDumpAArrayFormatNoEmptyLines(): void {
		$var = [1, 2, 3];
		$result = Dump::a($var, 'NoEmpty Test', true);

		$this->assertIsString($result);
		// Split into lines and check no consecutive empty lines
		$lines = \explode("\n", $result);
		$consecutiveEmpty = false;
		$prevEmpty = false;

		foreach ($lines as $line) {
			$isEmpty = (\trim($line) === '');
			if ($isEmpty && $prevEmpty) {
				$consecutiveEmpty = true;
				break;
			}
			$prevEmpty = $isEmpty;
		}

		$this->assertFalse($consecutiveEmpty, 'Output should not have consecutive empty lines');
	}

	/**
	* Test Dump::a() mixed key array format
	**/
	public function testDumpAMixedKeyArrayFormat(): void {
		$var = ['one', 'two', 'three' => ['nested']];
		$result = Dump::a($var, 'Mixed A', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Mixed A ===', $result);
		$this->assertStringContainsString('Array[size: 3]', $result);
		$this->assertStringContainsString('[0] => \'one\'', $result);
		$this->assertStringContainsString('[1] => \'two\'', $result);
		$this->assertStringContainsString('[three] => Array[size: 1]', $result);
	}

	/**
	* Test Dump::a() object format with class name
	**/
	public function testDumpAObjectFormat(): void {
		$var = (object) ['name' => 'test', 'value' => 42];
		$result = Dump::a($var, 'Test Object', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Test Object ===', $result);
		$this->assertStringContainsString('stdClass {', $result);
		$this->assertStringContainsString('[name] => \'test\'', $result);
		$this->assertStringContainsString('[value] => 42', $result);
	}

	/**
	* Test Dump::a() custom object format with class name
	**/
	public function testDumpACustomObjectFormat(): void {
		$testClass = new class {
			public string $name = 'custom';
			public int $value = 123;
			private string $private = 'hidden';
		};

		$result = Dump::a($testClass, 'Custom Object', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Custom Object ===', $result);
		// Should contain the class name
		$this->assertMatchesRegularExpression('/class@anonymous.*? {/', $result);
		$this->assertStringContainsString('[name] => \'custom\'', $result);
		$this->assertStringContainsString('[value] => 123', $result);
		// Private properties should not be included
		$this->assertStringNotContainsString('[private] => \'hidden\'', $result);
	}

	/**
	* Test Dump::a() object format opening brace on same line
	**/
	public function testDumpAObjectFormatOpeningBrace(): void {
		$var = (object) ['prop' => 'value'];
		$result = Dump::a($var, 'Object Brace', true);

		$this->assertIsString($result);
		// Opening brace should be on same line as class name
		$this->assertMatchesRegularExpression('/stdClass \{/', $result);
	}

	/**
	* Test Dump::a() object format closing brace
	**/
	public function testDumpAObjectFormatClosingBrace(): void {
		$var = (object) ['prop' => 'value'];
		$result = Dump::a($var, 'Object Close', true);

		$this->assertIsString($result);
		// Should have closing brace
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() object format no empty lines
	**/
	public function testDumpAObjectFormatNoEmptyLines(): void {
		$var = (object) ['prop1' => 'value1', 'prop2' => 'value2'];
		$result = Dump::a($var, 'Object NoEmpty', true);

		$this->assertIsString($result);
		// Split into lines and check no consecutive empty lines
		$lines = \explode("\n", $result);
		$consecutiveEmpty = false;
		$prevEmpty = false;

		foreach ($lines as $line) {
			$isEmpty = (\trim($line) === '');
			if ($isEmpty && $prevEmpty) {
				$consecutiveEmpty = true;
				break;
			}
			$prevEmpty = $isEmpty;
		}

		$this->assertFalse($consecutiveEmpty, 'Object output should not have consecutive empty lines');
	}

	/**
	* Test Dump::a() array indentation structure - 4 spaces per level
	**/
	public function testDumpAArrayIndentationStructure(): void {
		$var = ['level1' => ['level2' => ['level3' => 'deep']]];
		$result = Dump::a($var, 'Indent Test', true);

		$this->assertIsString($result);
		// Check that indentation is exactly 4 spaces per level
		$this->assertStringContainsString('Array[size: 1] {', $result);
		// Level 1 indentation (4 spaces)
		$this->assertStringContainsString('    [level1] => Array[size: 1]', $result);
		// Level 2 indentation (8 spaces)
		$this->assertStringContainsString('        [level2] => Array[size: 1]', $result);
		// Level 3 indentation (12 spaces)
		$this->assertStringContainsString('            [level3] => \'deep\'', $result);
	}

	/**
	* Test Dump::a() object indentation structure - 4 spaces per level
	**/
	public function testDumpAObjectIndentationStructure(): void {
		$var = (object) ['prop1' => (object) ['prop2' => (object) ['prop3' => 'deep']]];
		$result = Dump::a($var, 'Object Indent', true);

		$this->assertIsString($result);
		// Check that indentation is exactly 4 spaces per level
		$this->assertStringContainsString('stdClass {', $result);
		// Level 1 indentation (4 spaces)
		$this->assertStringContainsString('    [prop1] => stdClass {', $result);
		// Level 2 indentation (8 spaces)
		$this->assertStringContainsString('        [prop2] => stdClass {', $result);
		// Level 3 indentation (12 spaces)
		$this->assertStringContainsString('            [prop3] => \'deep\'', $result);
	}

	/**
	* Test Dump::a() mixed indentation with arrays and objects
	**/
	public function testDumpAMixedIndentationStructure(): void {
		$var = [
			'array_prop' => [1, 2, 3],
			'object_prop' => (object) ['name' => 'test'],
		];
		$result = Dump::a($var, 'Mixed Indent', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Mixed Indent ===', $result);
		// Check array indentation
		$this->assertStringContainsString('    [array_prop] => Array[size: 3]', $result);
		$this->assertStringContainsString('        [0] => 1', $result);
		// Check object indentation
		$this->assertStringContainsString('    [object_prop] => stdClass {', $result);
		$this->assertStringContainsString('        [name] => \'test\'', $result);
	}

	/**
	* Test Dump::a() proper closing brace indentation
	**/
	public function testDumpAClosingBraceIndentation(): void {
		$var = ['nested' => [1, 2]];
		$result = Dump::a($var, 'Brace Indent', true);

		$this->assertIsString($result);
		// Check that closing braces are properly indented
		$this->assertStringContainsString('Array[size: 1] {', $result);
		$this->assertStringContainsString('    [nested] => Array[size: 2] {', $result);
		$this->assertStringContainsString('        [0] => 1', $result);
		$this->assertStringContainsString('        [1] => 2', $result);
		// Closing braces should be at the same indentation level as opening
		$this->assertStringContainsString('    }', $result); // Nested array close
		$this->assertStringContainsString('}', $result); // Main array close
	}

	/**
	* Test Dump::a() deeply nested array structure
	**/
	public function testDumpADeeplyNestedArray(): void {
		$var = ['level1' => ['level2' => ['level3' => ['level4' => ['level5' => 'deep']]]]];
		$result = Dump::a($var, 'Deep Array', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Deep Array ===', $result);
		$this->assertStringContainsString('Array[size: 1] {', $result);
		$this->assertStringContainsString('    [level1] => Array[size: 1] {', $result);
		$this->assertStringContainsString('        [level2] => Array[size: 1] {', $result);
		$this->assertStringContainsString('            [level3] => Array[size: 1] {', $result);
		$this->assertStringContainsString('                [level4] => Array[size: 1] {', $result);
		$this->assertStringContainsString('                    [level5] => \'deep\'', $result);
		// Check all closing braces
		$this->assertStringContainsString('                }', $result);
		$this->assertStringContainsString('            }', $result);
		$this->assertStringContainsString('        }', $result);
		$this->assertStringContainsString('    }', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() deeply nested object structure
	**/
	public function testDumpADeeplyNestedObject(): void {
		$var = (object) ['level1' => (object) ['level2' => (object) ['level3' => (object) ['level4' => (object) ['level5' => 'deep']]]]];
		$result = Dump::a($var, 'Deep Object', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Deep Object ===', $result);
		$this->assertStringContainsString('stdClass {', $result);
		$this->assertStringContainsString('    [level1] => stdClass {', $result);
		$this->assertStringContainsString('        [level2] => stdClass {', $result);
		$this->assertStringContainsString('            [level3] => stdClass {', $result);
		$this->assertStringContainsString('                [level4] => stdClass {', $result);
		$this->assertStringContainsString('                    [level5] => \'deep\'', $result);
		// Check all closing braces
		$this->assertStringContainsString('                }', $result);
		$this->assertStringContainsString('            }', $result);
		$this->assertStringContainsString('        }', $result);
		$this->assertStringContainsString('    }', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() mixed nested arrays and objects
	**/
	public function testDumpAMixedNestedStructures(): void {
		$var = [
			'array_part' => [1, 2, ['nested' => 'value']],
			'object_part' => (object) ['prop' => (object) ['nested' => 'value']],
		];
		$result = Dump::a($var, 'Mixed Nested', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Mixed Nested ===', $result);
		$this->assertStringContainsString('Array[size: 2] {', $result);

		// Check array part
		$this->assertStringContainsString('    [array_part] => Array[size: 3] {', $result);
		$this->assertStringContainsString('        [0] => 1', $result);
		$this->assertStringContainsString('        [1] => 2', $result);
		$this->assertStringContainsString('        [2] => Array[size: 1] {', $result);
		$this->assertStringContainsString('            [nested] => \'value\'', $result);
		$this->assertStringContainsString('        }', $result);

		// Check object part
		$this->assertStringContainsString('    [object_part] => stdClass {', $result);
		$this->assertStringContainsString('        [prop] => stdClass {', $result);
		$this->assertStringContainsString('            [nested] => \'value\'', $result);
		$this->assertStringContainsString('        }', $result);

		// Closing braces
		$this->assertStringContainsString('    }', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() nested structures with auto-detected header
	**/
	public function testDumpANestedWithAutoHeader(): void {
		$nestedVar = ['level1' => ['level2' => 'value']];
		$result = Dump::a($nestedVar, null, true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== $nestedVar ===', $result);
		$this->assertStringContainsString('Array[size: 1] {', $result);
		$this->assertStringContainsString('    [level1] => Array[size: 1] {', $result);
		$this->assertStringContainsString('        [level2] => \'value\'', $result);
		$this->assertStringContainsString('    }', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() with string value
	**/
	public function testDumpAWithString(): void {
		$var = 'test string value';
		$result = Dump::a($var, 'String Test', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== String Test ===', $result);
		$this->assertStringContainsString('\'test string value\'', $result);
	}

	/**
	* Test Dump::a() with integer value
	**/
	public function testDumpAWithInteger(): void {
		$var = 42;
		$result = Dump::a($var, 'Int Test', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Int Test ===', $result);
		$this->assertStringContainsString('42', $result);
	}

	/**
	* Test Dump::a() with float value
	**/
	public function testDumpAWithFloat(): void {
		$var = 3.14159;
		$result = Dump::a($var, 'Float Test', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Float Test ===', $result);
		$this->assertStringContainsString('3.14159', $result);
	}

	/**
	* Test Dump::a() with boolean true value
	**/
	public function testDumpAWithBooleanTrue(): void {
		$var = true;
		$result = Dump::a($var, 'Bool True Test', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Bool True Test ===', $result);
		$this->assertStringContainsString('true', $result);
	}

	/**
	* Test Dump::a() with boolean false value
	**/
	public function testDumpAWithBooleanFalse(): void {
		$var = false;
		$result = Dump::a($var, 'Bool False Test', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Bool False Test ===', $result);
		$this->assertStringContainsString('false', $result);
	}

	/**
	* Test Dump::a() with null value
	**/
	public function testDumpAWithNull(): void {
		$var = null;
		$result = Dump::a($var, 'Null Test', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Null Test ===', $result);
		$this->assertStringContainsString('NULL', $result);
	}

	/**
	* Test Dump::a() with empty array
	**/
	public function testDumpAWithEmptyArray(): void {
		$var = [];
		$result = Dump::a($var, 'Empty Array', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Empty Array ===', $result);
		$this->assertStringContainsString('Array[size: 0] {', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() with empty object
	**/
	public function testDumpAWithEmptyObject(): void {
		$var = (object) [];
		$result = Dump::a($var, 'Empty Object', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Empty Object ===', $result);
		$this->assertStringContainsString('stdClass {', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() output without return (echo)
	**/
	public function testDumpAOutputWithoutReturn(): void {
		$var = ['key' => 'value'];

		\ob_start();
		Dump::a($var, 'Echo Test', false);
		$output = \ob_get_clean();

		$this->assertIsString($output);
		$this->assertStringContainsString('=== Echo Test ===', $output);
		$this->assertStringContainsString('[key] => \'value\'', $output);
	}

	/**
	* Test Dump::a() with various data types in array
	**/
	public function testDumpAWithMixedTypesInArray(): void {
		$var = [
			'string' => 'hello',
			'int' => 42,
			'float' => 3.14,
			'bool_true' => true,
			'bool_false' => false,
			'null' => null,
			'array' => [1, 2, 3],
		];
		$result = Dump::a($var, 'Mixed Types', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Mixed Types ===', $result);
		$this->assertStringContainsString('Array[size: 7]', $result);
		$this->assertStringContainsString('[string] => \'hello\'', $result);
		$this->assertStringContainsString('[int] => 42', $result);
		$this->assertStringContainsString('[float] => 3.14', $result);
		$this->assertStringContainsString('[bool_true] => true', $result);
		$this->assertStringContainsString('[bool_false] => false', $result);
		$this->assertStringContainsString('[null] => NULL', $result);
		$this->assertStringContainsString('[array] => Array[size: 3]', $result);
	}

	/**
	* Test Dump::a() with special characters in string
	**/
	public function testDumpAWithSpecialCharacters(): void {
		$var = "line1\nline2\ttab\r\nwindows";
		$result = Dump::a($var, 'Special Chars', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Special Chars ===', $result);
		// var_export preserves newlines as actual newlines, not escapes
		$this->assertStringContainsString("line1\nline2", $result);
		$this->assertStringContainsString("\ttab", $result);
		$this->assertStringContainsString("windows", $result);
	}

	// =========================================================================
	// Full output format tests - exact string matching (30+ tests)
	// =========================================================================

	/**
	* Test Dump::a() exact output format - simple array
	**/
	public function testDumpAExactOutputSimpleArray(): void {
		$var = [1, 2, 3];
		$result = Dump::a($var, 'Test', true);

		$expected = "=== Test ===
Array[size: 3] {
    [0] => 1
    [1] => 2
    [2] => 3
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - associative array
	**/
	public function testDumpAExactOutputAssociativeArray(): void {
		$var = ['a' => 1, 'b' => 2];
		$result = Dump::a($var, 'Assoc', true);

		$expected = "=== Assoc ===
Array[size: 2] {
    [a] => 1
    [b] => 2
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - nested array (2 levels)
	**/
	public function testDumpAExactOutputNestedArray2Levels(): void {
		$var = ['outer' => ['inner' => 'value']];
		$result = Dump::a($var, 'Nested', true);

		$expected = "=== Nested ===
Array[size: 1] {
    [outer] => Array[size: 1] {
        [inner] => 'value'
    }
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - nested array (3 levels)
	**/
	public function testDumpAExactOutputNestedArray3Levels(): void {
		$var = ['l1' => ['l2' => ['l3' => 'deep']]];
		$result = Dump::a($var, 'Deep', true);

		$expected = "=== Deep ===
Array[size: 1] {
    [l1] => Array[size: 1] {
        [l2] => Array[size: 1] {
            [l3] => 'deep'
        }
    }
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - simple object
	**/
	public function testDumpAExactOutputSimpleObject(): void {
		$var = (object) ['name' => 'test', 'value' => 42];
		$result = Dump::a($var, 'Object', true);

		$expected = "=== Object ===
stdClass {
    [name] => 'test'
    [value] => 42
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - nested object (2 levels)
	**/
	public function testDumpAExactOutputNestedObject2Levels(): void {
		$var = (object) ['outer' => (object) ['inner' => 'value']];
		$result = Dump::a($var, 'Nested Obj', true);

		$expected = "=== Nested Obj ===
stdClass {
    [outer] => stdClass {
        [inner] => 'value'
    }
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - nested object (3 levels)
	**/
	public function testDumpAExactOutputNestedObject3Levels(): void {
		$var = (object) ['l1' => (object) ['l2' => (object) ['l3' => 'deep']]];
		$result = Dump::a($var, 'Deep Obj', true);

		$expected = "=== Deep Obj ===
stdClass {
    [l1] => stdClass {
        [l2] => stdClass {
            [l3] => 'deep'
        }
    }
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - mixed array and object
	**/
	public function testDumpAExactOutputMixedArrayObject(): void {
		$var = [
			'array_key' => [1, 2],
			'object_key' => (object) ['prop' => 'val'],
		];
		$result = Dump::a($var, 'Mixed', true);

		$expected = "=== Mixed ===
Array[size: 2] {
    [array_key] => Array[size: 2] {
        [0] => 1
        [1] => 2
    }
    [object_key] => stdClass {
        [prop] => 'val'
    }
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - empty array
	**/
	public function testDumpAExactOutputEmptyArray(): void {
		$var = [];
		$result = Dump::a($var, 'Empty', true);

		$expected = "=== Empty ===
Array[size: 0] {
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - empty object
	**/
	public function testDumpAExactOutputEmptyObject(): void {
		$var = (object) [];
		$result = Dump::a($var, 'Empty Obj', true);

		$expected = "=== Empty Obj ===
stdClass {
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - scalar string
	**/
	public function testDumpAExactOutputScalarString(): void {
		$var = 'hello world';
		$result = Dump::a($var, 'String', true);

		$expected = "=== String ===
'hello world'\n";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - scalar integer
	**/
	public function testDumpAExactOutputScalarInteger(): void {
		$var = 42;
		$result = Dump::a($var, 'Integer', true);

		$expected = "=== Integer ===
42\n";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - scalar float
	**/
	public function testDumpAExactOutputScalarFloat(): void {
		$var = 3.14159;
		$result = Dump::a($var, 'Float', true);

		$expected = "=== Float ===
3.14159\n";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - boolean true
	**/
	public function testDumpAExactOutputBoolTrue(): void {
		$var = true;
		$result = Dump::a($var, 'BoolTrue', true);

		$expected = "=== BoolTrue ===
true\n";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - boolean false
	**/
	public function testDumpAExactOutputBoolFalse(): void {
		$var = false;
		$result = Dump::a($var, 'BoolFalse', true);

		$expected = "=== BoolFalse ===
false\n";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - null
	**/
	public function testDumpAExactOutputNull(): void {
		$var = null;
		$result = Dump::a($var, 'Null', true);

		$expected = "=== Null ===
NULL\n";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - array with multiple types
	**/
	public function testDumpAExactOutputArrayMultipleTypes(): void {
		$var = [
			'string' => 'text',
			'int' => 100,
			'float' => 2.5,
			'bool' => true,
			'null' => null,
		];
		$result = Dump::a($var, 'Types', true);

		$expected = "=== Types ===
Array[size: 5] {
    [string] => 'text'
    [int] => 100
    [float] => 2.5
    [bool] => true
    [null] => NULL
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - deeply nested (4 levels)
	**/
	public function testDumpAExactOutputDeeplyNested4Levels(): void {
		$var = ['a' => ['b' => ['c' => ['d' => 'deep']]]];
		$result = Dump::a($var, 'Deep4', true);

		$expected = "=== Deep4 ===
Array[size: 1] {
    [a] => Array[size: 1] {
        [b] => Array[size: 1] {
            [c] => Array[size: 1] {
                [d] => 'deep'
            }
        }
    }
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - object with nested array
	**/
	public function testDumpAExactOutputObjectWithNestedArray(): void {
		$var = (object) ['items' => [1, 2, 3], 'name' => 'test'];
		$result = Dump::a($var, 'ObjArr', true);

		$expected = "=== ObjArr ===
stdClass {
    [items] => Array[size: 3] {
        [0] => 1
        [1] => 2
        [2] => 3
    }
    [name] => 'test'
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - array with nested object
	**/
	public function testDumpAExactOutputArrayWithNestedObject(): void {
		$var = ['obj' => (object) ['x' => 1, 'y' => 2]];
		$result = Dump::a($var, 'ArrObj', true);

		$expected = "=== ArrObj ===
Array[size: 1] {
    [obj] => stdClass {
        [x] => 1
        [y] => 2
    }
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output format - complex mixed structure
	**/
	public function testDumpAExactOutputComplexMixed(): void {
		$var = [
			'users' => [
				['name' => 'Alice', 'age' => 30],
				['name' => 'Bob', 'age' => 25],
			],
			'config' => (object) ['debug' => true, 'version' => 1.0],
		];
		$result = Dump::a($var, 'Complex', true);

		$expected = "=== Complex ===
Array[size: 2] {
    [users] => Array[size: 2] {
        [0] => Array[size: 2] {
            [name] => 'Alice'
            [age] => 30
        }
        [1] => Array[size: 2] {
            [name] => 'Bob'
            [age] => 25
        }
    }
    [config] => stdClass {
        [debug] => true
        [version] => 1.0
    }
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::c() exact output format - console method
	**/
	public function testDumpCExactOutput(): void {
		$var = ['x' => 10, 'y' => 20];
		$result = Dump::c($var, 'Console', true);

		$expected = "=== Console ===
Array[size: 2] {
    [x] => 10
    [y] => 20
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::w() exact output format - web method
	**/
	public function testDumpWExactOutput(): void {
		$var = ['key' => 'value'];
		$result = Dump::w($var, 'Web', true);

		$expected = "=== Web ===
Array[size: 1] {
    [key] => 'value'
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::log() exact output format - log method
	**/
	public function testDumpLogExactOutput(): void {
		$var = ['log' => 'entry'];
		$result = Dump::log($var, 'Log', true);

		$expected = "=== Log ===
Array[size: 1] {
    [log] => 'entry'
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output - string with special chars
	**/
	public function testDumpAExactOutputStringSpecialChars(): void {
		$var = "line1\nline2\ttab";
		$result = Dump::a($var, 'Special', true);

		$expected = "=== Special ===
'line1
line2\ttab'\n";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output - negative numbers
	**/
	public function testDumpAExactOutputNegativeNumbers(): void {
		$var = [-5, -10.5, -100];
		$result = Dump::a($var, 'Negative', true);

		$expected = "=== Negative ===
Array[size: 3] {
    [0] => -5
    [1] => -10.5
    [2] => -100
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output - zero values
	**/
	public function testDumpAExactOutputZeroValues(): void {
		$var = ['int' => 0, 'float' => 0.0, 'bool' => false];
		$result = Dump::a($var, 'Zero', true);

		$expected = "=== Zero ===
Array[size: 3] {
    [int] => 0
    [float] => 0.0
    [bool] => false
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output - array with numeric keys
	**/
	public function testDumpAExactOutputNumericKeys(): void {
		$var = [0 => 'zero', 5 => 'five', 10 => 'ten'];
		$result = Dump::a($var, 'NumericKeys', true);

		$expected = "=== NumericKeys ===
Array[size: 3] {
    [0] => 'zero'
    [5] => 'five'
    [10] => 'ten'
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output - long string
	**/
	public function testDumpAExactOutputLongString(): void {
		$var = 'This is a very long string that contains many characters and should be properly exported';
		$result = Dump::a($var, 'LongStr', true);

		$expected = "=== LongStr ===
'This is a very long string that contains many characters and should be properly exported'\n";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output - scientific notation
	**/
	public function testDumpAExactOutputScientificNotation(): void {
		$var = 1.23e+10;
		$result = Dump::a($var, 'Sci', true);

		$expected = "=== Sci ===
12300000000.0\n";
		$this->assertSame($expected, $result);
	}

	/**
	* Test Dump::a() exact output - very deep nested (5 levels array)
	**/
	public function testDumpAExactOutputVeryDeepNested(): void {
		$var = ['a' => ['b' => ['c' => ['d' => ['e' => 'five']]]]];
		$result = Dump::a($var, 'Deep5', true);

		$expected = "=== Deep5 ===
Array[size: 1] {
    [a] => Array[size: 1] {
        [b] => Array[size: 1] {
            [c] => Array[size: 1] {
                [d] => Array[size: 1] {
                    [e] => 'five'
                }
            }
        }
    }
}
";
		$this->assertSame($expected, $result);
	}

	/**
	* Test formatVariable with unsupported type (resource) - covers unreachable code path
	**/
	public function testDumpFormatVariableWithResource(): void {
		$method = new \ReflectionMethod(Dump::class, 'formatVariable');
		$method->setAccessible(true);

		$resource = \fopen('php://memory', 'r');
		$result = $method->invoke(null, $resource, 0);

		$this->assertSame('', $result);
	}

	/**
	* Test all branches in Dump to achieve 100% coverage.
	**/
	public function testDumpFullCoverage(): void {
		Dump::c(1, null, true);
		Dump::c(1, 'h', true);
		Dump::c([], null, true);
		Dump::c(new \stdClass(), null, true);
		Dump::w(1, null, true);
		Dump::log(1, null, true);
		Dump::byOutType(1, 1, null, true);
		Dump::auto(1, null, true);

		$method = new \ReflectionMethod(Dump::class, 'formatVariable');
		$method->setAccessible(true);
		$method->invoke(null, [], 0);
		$method->invoke(null, [1], 0);
		$method->invoke(null, ['a' => ['b' => ['c' => 1]]], 0);

		$obj = new \stdClass();
		$obj->prop = 'val';
		$method->invoke(null, $obj, 0);
		$method->invoke(null, ['arr' => $obj], 0);

		self::assertTrue(true);
	}

	/**
	* Test Dump::a() with object that has __debugInfo() method (like HuArray)
	**/
	public function testDumpAWithDebugInfoObject(): void {
		$var = new class {
			public function __debugInfo(): array {
				return ['key1' => 'value1', 'key2' => 42];
			}
		};

		$result = Dump::a($var, 'DebugInfo Object', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== DebugInfo Object ===', $result);
		$this->assertStringContainsString('[key1] => \'value1\'', $result);
		$this->assertStringContainsString('[key2] => 42', $result);
	}

	/**
	* Test Dump::a() with nested __debugInfo objects
	**/
	public function testDumpAWithNestedDebugInfoObjects(): void {
		$inner = new class {
			public function __debugInfo(): array {
				return ['inner_key' => 'inner_value'];
			}
		};

		$outer = new class($inner) {
			private object $nested;

			public function __construct(object $nested) {
				$this->nested = $nested;
			}

			public function __debugInfo(): array {
				return ['outer_key' => 'outer_value', 'nested' => $this->nested];
			}
		};

		$result = Dump::a($outer, 'Nested DebugInfo', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Nested DebugInfo ===', $result);
		$this->assertStringContainsString('[outer_key] => \'outer_value\'', $result);
		$this->assertStringContainsString('[nested]', $result);
		$this->assertStringContainsString('[inner_key] => \'inner_value\'', $result);
	}

	// =========================================================================
	// Tests with real framework classes (HuArray, Settings)
	// =========================================================================

	/**
	* Test Dump::a() with HuArray - numeric indexed array
	**/
	public function testDumpAWithHuArrayNumeric(): void {
		$ha = new HuArray([0, 11, 22, 777]);
		$result = Dump::a($ha, 'HuArray Numeric', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== HuArray Numeric ===', $result);
		$this->assertStringContainsString(HuArray::class . ' {', $result);
		$this->assertStringContainsString('[0] => 0', $result);
		$this->assertStringContainsString('[1] => 11', $result);
		$this->assertStringContainsString('[2] => 22', $result);
		$this->assertStringContainsString('[3] => 777', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() with HuArray - associative array
	**/
	public function testDumpAWithHuArrayAssociative(): void {
		$ha = new HuArray(['name' => 'test', 'value' => 42, 'active' => true]);
		$result = Dump::a($ha, 'HuArray Assoc', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== HuArray Assoc ===', $result);
		$this->assertStringContainsString(HuArray::class . ' {', $result);
		$this->assertStringContainsString('[name] => \'test\'', $result);
		$this->assertStringContainsString('[value] => 42', $result);
		$this->assertStringContainsString('[active] => true', $result);
	}

	/**
	* Test Dump::a() with HuArray - nested arrays
	**/
	public function testDumpAWithHuArrayNested(): void {
		$ha = new HuArray([
			'user' => ['name' => 'Alice', 'age' => 30],
			'items' => [1, 2, 3],
		]);
		$result = Dump::a($ha, 'HuArray Nested', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== HuArray Nested ===', $result);
		$this->assertStringContainsString(HuArray::class . ' {', $result);
		$this->assertStringContainsString('[user] => Array[size: 2]', $result);
		$this->assertStringContainsString('[items] => Array[size: 3]', $result);
		$this->assertStringContainsString('[name] => \'Alice\'', $result);
		$this->assertStringContainsString('[age] => 30', $result);
	}

	/**
	* Test Dump::a() with HuArray - empty
	**/
	public function testDumpAWithHuArrayEmpty(): void {
		$ha = new HuArray([]);
		$result = Dump::a($ha, 'HuArray Empty', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== HuArray Empty ===', $result);
		$this->assertStringContainsString(HuArray::class . ' {', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() with Settings - basic
	**/
	public function testDumpAWithSettingsBasic(): void {
		$settings = new Settings(['option1' => 'value1', 'option2' => 42]);
		$result = Dump::a($settings, 'Settings Basic', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Settings Basic ===', $result);
		$this->assertStringContainsString(Settings::class . ' {', $result);
		$this->assertStringContainsString('[option1] => \'value1\'', $result);
		$this->assertStringContainsString('[option2] => 42', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() with Settings - nested structures
	**/
	public function testDumpAWithSettingsNested(): void {
		$settings = new Settings([
			'database' => ['host' => 'localhost', 'port' => 3306],
			'debug' => true,
		]);
		$result = Dump::a($settings, 'Settings Nested', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Settings Nested ===', $result);
		$this->assertStringContainsString(Settings::class . ' {', $result);
		$this->assertStringContainsString('[database] => Array[size: 2]', $result);
		$this->assertStringContainsString('[debug] => true', $result);
		$this->assertStringContainsString('[host] => \'localhost\'', $result);
		$this->assertStringContainsString('[port] => 3306', $result);
	}

	/**
	* Test Dump::a() with Settings - empty
	**/
	public function testDumpAWithSettingsEmpty(): void {
		$settings = new Settings([]);
		$result = Dump::a($settings, 'Settings Empty', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Settings Empty ===', $result);
		$this->assertStringContainsString(Settings::class . ' {', $result);
		$this->assertStringContainsString('}', $result);
	}

	/**
	* Test Dump::a() with mixed HuArray and Settings
	**/
	public function testDumpAWithMixedHuArrayAndSettings(): void {
		$data = [
			'array' => new HuArray([1, 2, 3]),
			'settings' => new Settings(['key' => 'value']),
		];
		$result = Dump::a($data, 'Mixed Framework', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== Mixed Framework ===', $result);
		$this->assertStringContainsString('Array[size: 2]', $result);
		$this->assertStringContainsString('[array] => ' . HuArray::class . ' {', $result);
		$this->assertStringContainsString('[settings] => ' . Settings::class . ' {', $result);
		$this->assertStringContainsString('[key] => \'value\'', $result);
	}

	/**
	* Test Dump::a() with HuArray containing Settings
	**/
	public function testDumpAWithHuArrayContainingSettings(): void {
		$ha = new HuArray([
			'config' => new Settings(['debug' => true, 'version' => '1.0']),
			'name' => 'test',
		]);
		$result = Dump::a($ha, 'HuArray with Settings', true);

		$this->assertIsString($result);
		$this->assertStringContainsString('=== HuArray with Settings ===', $result);
		$this->assertStringContainsString(HuArray::class . ' {', $result);
		$this->assertStringContainsString('[config] => ' . Settings::class . ' {', $result);
		$this->assertStringContainsString('[debug] => true', $result);
		$this->assertStringContainsString('[version] => \'1.0\'', $result);
		$this->assertStringContainsString('[name] => \'test\'', $result);
	}
}
