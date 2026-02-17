<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseWhere;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseWhere
 */
class DatabaseWhereTest extends TestCase {
	public function testConstructorWithEmptyArray(): void {
		$where = new DatabaseWhere();

		$this->assertInstanceOf(DatabaseWhere::class, $where);
		$this->assertEquals('', $where->getSQL());
	}

	public function testConstructorWithSimpleWhere(): void {
		$where = new DatabaseWhere([
			['field1', 'value1'],
			['field2', 'value2']
		]);

		$sql = $where->getSQL();
		$this->assertIsString($sql);
	}

	public function testConstructorWithOperators(): void {
		$where = new DatabaseWhere([
			['id', 5, '>'],
			['status', 'active']
		]);

		$sql = $where->getSQL();
		$this->assertIsString($sql);
	}

	public function testSetArray(): void {
		$where = new DatabaseWhere();
		$where->setArray([
			['name', 'John']
		]);

		$sql = $where->getSQL();
		$this->assertIsString($sql);
	}

	public function testGetSQL(): void {
		$where = new DatabaseWhere([
			['field', 'value']
		]);

		$sql = $where->getSQL();
		$this->assertIsString($sql);
	}

	public function testGetArray(): void {
		$where = new DatabaseWhere([
			['field1', 'value1']
		]);

		$arr = $where->getArray();
		$this->assertIsArray($arr);
	}

	public function testCount(): void {
		$where = new DatabaseWhere([
			['field1', 'value1'],
			['field2', 'value2']
		]);

		$count = $where->count();
		$this->assertEquals(2, $count);
	}
}
