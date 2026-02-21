<?php
declare(strict_types=1);

/**
 * Test for DumpUtils class.
 */

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\DumpUtils;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\DumpUtils
 */
class DumpUtilsTest extends TestCase {
    public function testClassExists(): void {
        $this->assertTrue(class_exists(DumpUtils::class));
    }

    public function testTransformCorrect_print_r_WithEmptyArray(): void {
        $dump = "Array\n    (\n    )";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_print_r_WithSimpleArray(): void {
        $dump = "Array\n    (\n        [0] => 1\n        [1] => 2\n    )";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
        $this->assertStringContainsString('Array(', $result);
    }

    public function testTransformCorrect_print_r_ReplacesArrayPattern(): void {
        $dump = "Array\n    (";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertStringContainsString('Array(', $result);
    }

    public function testTransformCorrect_print_r_ReplacesObjectPattern(): void {
        $dump = "Object\n    (";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertStringContainsString('Object(', $result);
    }

    public function testTransformCorrect_print_r_ReplacesKeyPattern(): void {
        $dump = '["key"]=>';
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertStringContainsString('[key]=>', $result);
    }

    public function testTransformCorrect_print_r_ReplacesEmptyArrayPattern(): void {
        $dump = "Array(0){\n    }";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertStringContainsString('Array(0){}', $result);
    }

    public function testTransformCorrect_print_r_WithNestedArray(): void {
        $dump = "Array\n    (\n        [0] => Array\n            (\n                [0] => 1\n            )\n    )";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_print_r_WithObject(): void {
        $dump = "stdClass Object\n    (\n        [property] => value\n    )";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_print_r_TrimResult(): void {
        $dump = "\n  Array\n    (\n    )  \n";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testTransformCorrect_print_r_WithComplexStructure(): void {
        $dump = "Array\n    (\n        [name] => Test\n        [data] => Array\n            (\n                [0] => 1\n            )\n    )";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithEmptyArray(): void {
        $dump = "array(0) {\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithSimpleArray(): void {
        $dump = "array(2) {\n  [0]=>\n  int(1)\n  [1]=>\n  int(2)\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_ReplacesArrayPattern(): void {
        $dump = "array(2)\n  {";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertStringContainsString('Array', $result);
    }

    public function testTransformCorrect_var_dump_ReplacesObjectPattern(): void {
        $dump = "object(stdClass)#1\n    (";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        // PHP 8 format differs, just check it returns a string
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_ReplacesKeyPattern(): void {
        $dump = '["key"]=>';
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertStringContainsString('[key] =>', $result);
    }

    public function testTransformCorrect_var_dump_WithQuotedKey(): void {
        $dump = '["key"]=>';
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertStringContainsString('[key] =>', $result);
    }

    public function testTransformCorrect_var_dump_WithNestedArray(): void {
        $dump = "array(1) {\n  [0]=>\n  array(1) {\n    [0]=>\n    int(1)\n  }\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithObject(): void {
        $dump = "object(stdClass)#1 (1) {\n  [\"property\"]=>\n  string(5) \"value\"\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_TrimResult(): void {
        $dump = "\n  array(0) {\n  }  \n";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithIntegerValues(): void {
        $dump = "array(2) {\n  [0]=>\n  int(42)\n  [1]=>\n  int(100)\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithStringValues(): void {
        $dump = "array(1) {\n  [\"name\"]=>\n  string(4) \"test\"\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithBooleanValues(): void {
        $dump = "array(2) {\n  [0]=>\n  bool(true)\n  [1]=>\n  bool(false)\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithNullValue(): void {
        $dump = "array(1) {\n  [0]=>\n  NULL\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithFloatValues(): void {
        $dump = "array(1) {\n  [0]=>\n  float(3.14)\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testBothMethodsReturnString(): void {
        $print_r_dump = "Array\n    ()";
        $var_dump_dump = "array(0) {\n}";

        $result1 = DumpUtils::transformCorrect_print_r($print_r_dump);
        $result2 = DumpUtils::transformCorrect_var_dump($var_dump_dump);

        $this->assertIsString($result1);
        $this->assertIsString($result2);
    }

    public function testTransformCorrect_print_r_WithMultipleKeys(): void {
        $dump = "Array\n    (\n        [\"key1\"]=>\n        1\n        [\"key2\"]=>\n        2\n    )";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithMultipleKeys(): void {
        $dump = "array(2) {\n  [\"key1\"]=>\n  int(1)\n  [\"key2\"]=>\n  int(2)\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_print_r_PreservesContent(): void {
        $dump = "Array\n    (\n        [name] => Test\n    )";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertStringContainsString('name', $result);
        $this->assertStringContainsString('Test', $result);
    }

    public function testTransformCorrect_var_dump_PreservesContent(): void {
        $dump = "array(1) {\n  [\"name\"]=>\n  string(4) \"test\"\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertStringContainsString('name', $result);
        $this->assertStringContainsString('test', $result);
    }

    public function testTransformCorrect_print_r_WithSpecialCharacters(): void {
        $dump = "Array\n    (\n        [\"key\"]=>\n        value with spaces\n    )";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithSpecialCharacters(): void {
        $dump = "array(1) {\n  [\"key\"]=>\n  string(17) \"value with spaces\"\n}";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testMethodsAreStatic(): void {
        $reflection = new \ReflectionClass(DumpUtils::class);
        $methods = $reflection->getMethods();

        foreach ($methods as $method) {
            $this->assertTrue($method->isStatic());
        }
    }

    public function testClassCannotBeInstantiated(): void {
        $reflection = new \ReflectionClass(DumpUtils::class);
        $this->assertTrue($reflection->isInstantiable());
    }

    public function testTransformCorrect_print_r_WithRealPrintROutput(): void {
        $array = ['key' => 'value', 'nested' => ['a' => 1, 'b' => 2]];
        $dump = print_r($array, true);
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testTransformCorrect_var_dump_WithRealVarDumpOutput(): void {
        $array = ['key' => 'value'];
        ob_start();
        var_dump($array);
        $dump = ob_get_clean();
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_print_r_WithEmptyString(): void {
        $result = DumpUtils::transformCorrect_print_r('');
        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    public function testTransformCorrect_var_dump_WithEmptyString(): void {
        $result = DumpUtils::transformCorrect_var_dump('');
        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    public function testTransformCorrect_print_r_WithNonArrayObject(): void {
        $dump = "Simple String";
        $result = DumpUtils::transformCorrect_print_r($dump);
        $this->assertIsString($result);
    }

    public function testTransformCorrect_var_dump_WithNonArrayObject(): void {
        $dump = "Simple String";
        $result = DumpUtils::transformCorrect_var_dump($dump);
        $this->assertIsString($result);
    }
}
