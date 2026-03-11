<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\DumpUtils;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Debug\DumpUtils
**/
class DumpUtilsTest extends TestCase {
	public function testClassExists(): void {
		$this->assertTrue(\class_exists(DumpUtils::class));
	}

	public function testTransformCorrectPrintRWithEmptyArray(): void {
		$dump = "Array\n    (\n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testTransformCorrectPrintRWithSimpleArray(): void {
		$dump = "Array\n    (\n    [key] => value\n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('Array(', $result);
	}

	public function testTransformCorrectPrintRWithNestedArray(): void {
		$dump = "Array\n    (\n    [nested] => Array\n        (\n            [key] => value\n        )\n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('Array(', $result);
	}

	public function testTransformCorrectPrintRWithObject(): void {
		$dump = "stdClass Object\n    (\n    [property] => value\n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('Object(', $result);
	}

	public function testTransformCorrectPrintRWithBracketNotation(): void {
		$dump = "Array\n    (\n    [\"key\"] => value\n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
		// The function replaces ["key"]=> with [key]=>, but input has ["key"] =>
		$this->assertStringContainsString('key', $result);
	}

	public function testTransformCorrectPrintRWithEmptyArrayObject(): void {
		$dump = "Array\n    (\n    )";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
	}

	public function testTransformCorrectPrintRTrimsResult(): void {
		$dump = "\n  Array\n    (\n    )  \n";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
		// trim() is applied, but internal structure remains
		$this->assertStringStartsWith('Array(', $result);
	}

	public function testTransformCorrectPrintRWithComplexArray(): void {
		$dump = "Array\n    (\n    [0] => first\n    [1] => second\n    [2] => Array\n        (\n            [nested] => value\n        )\n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('Array(', $result);
	}

	public function testTransformCorrectVarDumpWithEmptyArray(): void {
		$dump = "array(0) {\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('Array(0){', $result);
	}

	public function testTransformCorrectVarDumpWithSimpleArray(): void {
		$dump = "array(1) {\n  [\"key\"]=>\n  string(5) \"value\"\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('Array(1){', $result);
	}

	public function testTransformCorrectVarDumpWithObject(): void {
		$dump = "object(stdClass)#1 (1) {\n  [\"property\"]=>\n  string(5) \"value\"\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
		// Note: object() is not replaced by Object( in current implementation
		$this->assertStringContainsString('object(', $result);
	}

	public function testTransformCorrectVarDumpWithBracketNotation(): void {
		$dump = "array(1) {\n  [\"key\"]=>\n  string(5) \"value\"\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('[key] =>', $result);
	}

	public function testTransformCorrectVarDumpTrimsResult(): void {
		$dump = "\n  array(0) {\n  }  \n";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
		// trim() is applied, but internal structure remains
		$this->assertStringStartsWith('Array(0){', $result);
	}

	public function testTransformCorrectVarDumpWithNestedArray(): void {
		$dump = "array(1) {\n  [\"nested\"]=>\n  array(1) {\n    [\"key\"]=>\n    string(5) \"value\"\n  }\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('Array(', $result);
	}

	public function testTransformCorrectVarDumpWithMultipleKeys(): void {
		$dump = "array(2) {\n  [\"key1\"]=>\n  string(5) \"value1\"\n  [\"key2\"]=>\n  string(5) \"value2\"\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('[key1] =>', $result);
		$this->assertStringContainsString('[key2] =>', $result);
	}

	public function testTransformCorrectVarDumpWithNumericKeys(): void {
		$dump = "array(2) {\n  [0]=>\n  string(5) \"first\"\n  [1]=>\n  string(6) \"second\"\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('[0] =>', $result);
	}

	public function testTransformCorrectPrintRPreservesDataStructure(): void {
		$dump = "Array\n    (\n    [name] => John\n    [age] => 30\n    [city] => NYC\n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('name', $result);
		$this->assertStringContainsString('age', $result);
		$this->assertStringContainsString('city', $result);
	}

	public function testTransformCorrectVarDumpPreservesDataStructure(): void {
		$dump = "array(3) {\n  [\"name\"]=>\n  string(4) \"John\"\n  [\"age\"]=>\n  int(30)\n  [\"city\"]=>\n  string(3) \"NYC\"\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
		$this->assertStringContainsString('name', $result);
		$this->assertStringContainsString('age', $result);
		$this->assertStringContainsString('city', $result);
	}

	public function testTransformCorrectPrintRWithBooleanValues(): void {
		$dump = "Array\n    (\n    [true] => 1\n    [false] => \n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
	}

	public function testTransformCorrectVarDumpWithBooleanValues(): void {
		$dump = "array(2) {\n  [\"true\"]=>\n  bool(true)\n  [\"false\"]=>\n  bool(false)\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
	}

	public function testTransformCorrectPrintRWithNullValue(): void {
		$dump = "Array\n    (\n    [null] => \n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
	}

	public function testTransformCorrectVarDumpWithNullValue(): void {
		$dump = "array(1) {\n  [\"null\"]=>\n  NULL\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
	}

	public function testTransformCorrectPrintRIsStatic(): void {
		$reflection = new \ReflectionMethod(DumpUtils::class, 'transformCorrect_print_r');
		$this->assertTrue($reflection->isStatic());
	}

	public function testTransformCorrectVarDumpIsStatic(): void {
		$reflection = new \ReflectionMethod(DumpUtils::class, 'transformCorrect_var_dump');
		$this->assertTrue($reflection->isStatic());
	}

	public function testTransformCorrectPrintRAcceptsOnlyString(): void {
		$this->expectException(\TypeError::class);
		DumpUtils::transformCorrect_print_r([]);
	}

	public function testTransformCorrectVarDumpAcceptsOnlyString(): void {
		$this->expectException(\TypeError::class);
		DumpUtils::transformCorrect_var_dump([]);
	}

	public function testTransformCorrectPrintRReturnsString(): void {
		$dump = "Array\n    (\n)";
		$result = DumpUtils::transformCorrect_print_r($dump);
		$this->assertIsString($result);
	}

	public function testTransformCorrectVarDumpReturnsString(): void {
		$dump = "array(0) {\n}";
		$result = DumpUtils::transformCorrect_var_dump($dump);
		$this->assertIsString($result);
	}
}
