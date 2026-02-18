<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use PHPUnit\Framework\TestCase;
use Hubbitus\HuPHP\Debug\Dump;

/**
* @covers \Hubbitus\HuPHP\Debug\Dump
*/
class DumpTest extends TestCase {
	public function testConsoleDump(): void {
		$var = ['test' => 'value', 'number' => 123];
		$output = Dump::c($var, 'Test Header', true);

		$this->assertIsString($output);
		$this->assertStringContainsString('Test Header', $output);
		$this->assertStringContainsString('test', $output);
		$this->assertStringContainsString('value', $output);
	}

	public function testConsoleDumpWithHeader(): void {
		$var = 'simple string';
		$output = Dump::c($var, 'My Header', true);

		$this->assertStringContainsString('=== My Header ===', $output);
		$this->assertStringContainsString('simple string', $output);
	}

	public function testConsoleDumpWithoutHeader(): void {
		$var = 42;
		$output = Dump::c($var, null, true);

		$this->assertStringNotContainsString('===', $output);
		$this->assertStringContainsString('42', $output);
	}

	public function testWebDump(): void {
		$var = ['key' => 'value'];
		$output = Dump::w($var, 'Web Header', true);

		$this->assertIsString($output);
		$this->assertStringContainsString('Web Header', $output);
		$this->assertStringContainsString('key', $output);
	}

	public function testLogDump(): void {
		$var = 'log message';
		$output = Dump::log($var, 'Log Header', true);

		$this->assertIsString($output);
		$this->assertStringContainsString('Log Header', $output);
		$this->assertStringContainsString('log message', $output);
	}

	public function testAliasDump(): void {
		$var = ['test' => 'data'];
		$output = Dump::a($var, null, true);

		$this->assertIsString($output);
		$this->assertStringContainsString('test', $output);
	}

	public function testDumpArray(): void {
		$var = ['a' => 1, 'b' => 2, 'c' => 3];
		$output = Dump::c($var, null, true);

		$this->assertStringContainsString('Array', $output);
		$this->assertStringContainsString('a', $output);
		$this->assertStringContainsString('b', $output);
		$this->assertStringContainsString('c', $output);
	}

	public function testDumpObject(): void {
		$obj = new \stdClass();
		$obj->prop1 = 'value1';
		$obj->prop2 = 42;

		$output = Dump::c($obj, null, true);

		$this->assertStringContainsString('stdClass', $output);
		$this->assertStringContainsString('prop1', $output);
		$this->assertStringContainsString('value1', $output);
		$this->assertStringContainsString('prop2', $output);
	}

	public function testDumpNull(): void {
		$output = Dump::c(null, null, true);

		$this->assertStringContainsString('NULL', $output);
	}

	public function testDumpBoolean(): void {
		$output1 = Dump::c(true, null, true);
		$this->assertStringContainsString('true', $output1);

		$output2 = Dump::c(false, null, true);
		$this->assertStringContainsString('false', $output2);
	}

	public function testByOutType(): void {
		$var = ['test' => 'data'];
		$output = Dump::byOutType(0, $var, 'Header', true);

		$this->assertIsString($output);
		$this->assertStringContainsString('Header', $output);
	}

	public function testAutoDump(): void {
		$var = ['auto' => 'detect'];
		$output = Dump::auto($var, 'Auto Test', true);

		$this->assertIsString($output);
		$this->assertStringContainsString('Auto Test', $output);
	}

	public function testGenerateOutputWithArray(): void {
		$var = ['nested' => ['deep' => 'value']];
		$output = Dump::c($var, null, true);

		$this->assertStringContainsString('nested', $output);
		$this->assertStringContainsString('deep', $output);
	}

	public function testGenerateOutputWithObject(): void {
		$var = new class {
			public $property = 'test';
		};

		$output = Dump::c($var, null, true);

		$this->assertStringContainsString('property', $output);
		$this->assertStringContainsString('test', $output);
	}

	public function testGenerateOutputWithScalar(): void {
		$output1 = Dump::c('string value', null, true);
		$this->assertStringContainsString('string value', $output1);

		$output2 = Dump::c(123.45, null, true);
		$this->assertStringContainsString('123.45', $output2);
	}
}
