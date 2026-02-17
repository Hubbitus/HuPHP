<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\DumpUtils;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\DumpUtils
 */
class DumpUtilsTest extends TestCase {
	public function testTransformCorrectPrintR(): void {
		$result = DumpUtils::transformCorrect_print_r('test');

		$this->assertIsString($result);
	}

	public function testTransformCorrectPrintRWithObject(): void {
		$obj = new \stdClass();
		$obj->prop = 'value';

		ob_start();
		print_r($obj);
		$dump = ob_get_clean();

		$result = DumpUtils::transformCorrect_print_r($dump);

		$this->assertIsString($result);
	}

	public function testTransformCorrectVarDump(): void {
		$result = DumpUtils::transformCorrect_var_dump('test');

		$this->assertIsString($result);
	}

	public function testTransformCorrectVarDumpWithObject(): void {
		$obj = new \stdClass();
		$obj->prop = 'value';

		ob_start();
		var_dump($obj);
		$dump = ob_get_clean();

		$result = DumpUtils::transformCorrect_var_dump($dump);

		$this->assertIsString($result);
	}
}