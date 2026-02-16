<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\DumpUtils;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\DumpUtils
 */
class DumpUtilsTest extends TestCase {
	public function testTransformCorrectPrintRSimpleArray(): void {
		$array = ['a' => 1, 'b' => 2];
		$dump = print_r($array, true);

		$result = DumpUtils::transformCorrect_print_r($dump);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testTransformCorrectPrintRNestedArray(): void {
		$array = ['a' => ['b' => 2, 'c' => 3], 'd' => 4];
		$dump = print_r($array, true);

		$result = DumpUtils::transformCorrect_print_r($dump);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testTransformCorrectPrintREmptyArray(): void {
		$array = [];
		$dump = print_r($array, true);

		$result = DumpUtils::transformCorrect_print_r($dump);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testTransformCorrectVarDumpSimpleArray(): void {
		$array = ['a' => 1, 'b' => 2];
		ob_start();
		var_dump($array);
		$dump = ob_get_clean();

		$result = DumpUtils::transformCorrect_var_dump($dump);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testTransformCorrectVarDumpNestedArray(): void {
		$array = ['a' => ['b' => 2], 'c' => 3];
		ob_start();
		var_dump($array);
		$dump = ob_get_clean();

		$result = DumpUtils::transformCorrect_var_dump($dump);

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testTransformCorrectPrintRTrimsOutput(): void {
		$array = ['test' => 'value'];
		$dump = print_r($array, true);

		$result = DumpUtils::transformCorrect_print_r($dump);

		// Should be trimmed
		$this->assertEquals(trim($result), $result);
	}

	public function testTransformCorrectVarDumpTrimsOutput(): void {
		$array = ['test' => 'value'];
		ob_start();
		var_dump($array);
		$dump = ob_get_clean();

		$result = DumpUtils::transformCorrect_var_dump($dump);

		// Should be trimmed
		$this->assertEquals(trim($result), $result);
	}
}
