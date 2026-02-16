<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseOperators;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseOperators
 */
class DatabaseOperatorsTest extends TestCase {
	public function testOperators1IsArray(): void {
		$this->assertIsArray(DatabaseOperators::$operators1);
		$this->assertNotEmpty(DatabaseOperators::$operators1);
	}

	public function testOperators1ContainsExpectedValues(): void {
		$this->assertContains('BINARY', DatabaseOperators::$operators1);
		$this->assertContains('COLLATE', DatabaseOperators::$operators1);
		$this->assertContains('~', DatabaseOperators::$operators1);
		$this->assertContains('-', DatabaseOperators::$operators1);
	}

	public function testOperators2IsArray(): void {
		$this->assertIsArray(DatabaseOperators::$operators2);
		$this->assertNotEmpty(DatabaseOperators::$operators2);
	}

	public function testOperators2ContainsExpectedValues(): void {
		$this->assertContains('>>', DatabaseOperators::$operators2);
		$this->assertContains('*', DatabaseOperators::$operators2);
		$this->assertContains('-', DatabaseOperators::$operators2);
		$this->assertContains('LIKE', DatabaseOperators::$operators2);
		$this->assertContains('=', DatabaseOperators::$operators2);
		$this->assertContains('>', DatabaseOperators::$operators2);
		$this->assertContains('<', DatabaseOperators::$operators2);
	}

	public function testOperators3IsArray(): void {
		$this->assertIsArray(DatabaseOperators::$operators3);
		$this->assertNotEmpty(DatabaseOperators::$operators3);
	}

	public function testOperators3ContainsExpectedValues(): void {
		$this->assertContains('BETWEEN', DatabaseOperators::$operators3);
		$this->assertContains('NOT BETWEEN', DatabaseOperators::$operators3);
	}

	public function testOperatorsLogicalIsArray(): void {
		$this->assertIsArray(DatabaseOperators::$operatorsLogical);
		$this->assertNotEmpty(DatabaseOperators::$operatorsLogical);
	}

	public function testOperatorsLogicalContainsExpectedValues(): void {
		$this->assertContains('AND', DatabaseOperators::$operatorsLogical);
		$this->assertContains('&&', DatabaseOperators::$operatorsLogical);
		$this->assertContains('XOR', DatabaseOperators::$operatorsLogical);
		$this->assertContains('OR', DatabaseOperators::$operatorsLogical);
		$this->assertContains('||', DatabaseOperators::$operatorsLogical);
	}

	public function testOperatorsFlowIsArray(): void {
		$this->assertIsArray(DatabaseOperators::$operatorsFlow);
		$this->assertNotEmpty(DatabaseOperators::$operatorsFlow);
	}

	public function testOperatorsFlowContainsExpectedValues(): void {
		$this->assertContains('CASE', DatabaseOperators::$operatorsFlow);
	}

	public function testAllOperatorsAreStrings(): void {
		foreach (DatabaseOperators::$operators1 as $op) {
			$this->assertIsString($op);
		}
		foreach (DatabaseOperators::$operators2 as $op) {
			$this->assertIsString($op);
		}
		foreach (DatabaseOperators::$operators3 as $op) {
			$this->assertIsString($op);
		}
		foreach (DatabaseOperators::$operatorsLogical as $op) {
			$this->assertIsString($op);
		}
		foreach (DatabaseOperators::$operatorsFlow as $op) {
			$this->assertIsString($op);
		}
	}
}
