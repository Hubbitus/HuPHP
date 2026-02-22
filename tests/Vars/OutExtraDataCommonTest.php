<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

use Hubbitus\HuPHP\Vars\OutExtraDataCommon;
use Hubbitus\HuPHP\System\OS;
use PHPUnit\Framework\TestCase;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;

class OutExtraDataCommonTest extends TestCase {
	public function testConstructor(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$this->assertInstanceOf(OutExtraDataCommon::class, $out);
	}

	public function testStrForConsole(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = $out->strForConsole();
		$this->assertIsString($result);
		$this->assertStringContainsString('test', $result);
	}

	public function testStrForFile(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = $out->strForFile();
		$this->assertIsString($result);
	}

	public function testStrForWeb(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = $out->strForWeb();
		$this->assertIsString($result);
	}

	public function testStrForPrint(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = $out->strForPrint();
		$this->assertIsString($result);
	}

	public function testStrByOutTypeBrowser(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = $out->strByOutType(OS::OUT_TYPE_BROWSER);
		$this->assertIsString($result);
	}

	public function testStrByOutTypeConsole(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = $out->strByOutType(OS::OUT_TYPE_CONSOLE);
		$this->assertIsString($result);
	}

	public function testStrByOutTypeFile(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = $out->strByOutType(OS::OUT_TYPE_FILE);
		$this->assertIsString($result);
	}

	public function testStrByOutTypePrint(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = $out->strByOutType(OS::OUT_TYPE_PRINT);
		$this->assertIsString($result);
	}

	public function testStrByOutTypeInvalid(): void {
		// With strict types, TypeError is thrown for invalid type
		$this->expectException(\TypeError::class);
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$out->strByOutType('invalid_type');
	}

	public function testStrForPrintBase(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = OutExtraDataCommon::strForPrintBase($out);
		$this->assertIsString($result);
	}

	public function testStrByOutTypeBase(): void {
		$var = ['test' => 'value'];
		$out = new OutExtraDataCommon($var);
		$result = OutExtraDataCommon::strByOutTypeBase($out, OS::OUT_TYPE_CONSOLE);
		$this->assertIsString($result);
	}
}
