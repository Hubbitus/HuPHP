<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Database;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Database\DatabaseException;
use Hubbitus\HuPHP\Exceptions\Database\DatabaseQueryFailedException;
use Hubbitus\HuPHP\Database\IDatabase;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Database\DatabaseQueryFailedException
 */
class DatabaseQueryFailedExceptionTest extends TestCase {
	public function testConstructor(): void {
		$mockDb = $this->createMock(IDatabase::class);
		$exception = new DatabaseQueryFailedException('Query failed', $mockDb);

		$this->assertInstanceOf(DatabaseQueryFailedException::class, $exception);
		$this->assertInstanceOf(DatabaseException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Query failed', $exception->getMessage());
		$this->assertSame($mockDb, $exception->db);
	}

	public function testInheritsBaseExceptionMethods(): void {
		$mockDb = $this->createMock(IDatabase::class);
		$exception = new DatabaseQueryFailedException('Base', $mockDb);
		$exception->ADDMessage(' - end');

		$this->assertEquals('Base - end', $exception->getMessage());
	}
}
