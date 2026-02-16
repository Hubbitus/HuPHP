<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Database;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Database\DatabaseException;
use Hubbitus\HuPHP\Database\IDatabase;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Database\DatabaseException
 */
class DatabaseExceptionTest extends TestCase {
	public function testConstructor(): void {
		// Mock Database object
		$mockDb = $this->createMock(IDatabase::class);

		$exception = new DatabaseException('Test error', $mockDb);

		$this->assertInstanceOf(DatabaseException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Test error', $exception->getMessage());
		$this->assertSame($mockDb, $exception->db);
	}

	public function testDbPropertyIsSet(): void {
		$mockDb = $this->createMock(IDatabase::class);
		$exception = new DatabaseException('Error', $mockDb);

		$this->assertIsObject($exception->db);
		$this->assertSame($mockDb, $exception->db);
	}
}
