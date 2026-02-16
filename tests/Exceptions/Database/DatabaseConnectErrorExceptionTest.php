<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions\Database;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Database\DatabaseException;
use Hubbitus\HuPHP\Exceptions\Database\DatabaseConnectErrorException;
use Hubbitus\HuPHP\Database\IDatabase;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Database\DatabaseConnectErrorException
 */
class DatabaseConnectErrorExceptionTest extends TestCase {
	public function testConstructor(): void {
		$mockDb = $this->createMock(IDatabase::class);
		$exception = new DatabaseConnectErrorException('Connection failed', $mockDb);

		$this->assertInstanceOf(DatabaseConnectErrorException::class, $exception);
		$this->assertInstanceOf(DatabaseException::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertEquals('Connection failed', $exception->getMessage());
		$this->assertSame($mockDb, $exception->db);
	}

	public function testDBErrorPropertyExists(): void {
		$mockDb = $this->createMock(IDatabase::class);
		$exception = new DatabaseConnectErrorException('Error', $mockDb);

		$this->assertObjectHasProperty('DBError', $exception);
	}
}
