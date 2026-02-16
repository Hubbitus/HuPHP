<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseError;
use Hubbitus\HuPHP\Database\DatabaseErrorSettings;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseError
 * @covers \Hubbitus\HuPHP\Database\DBError_settings
 */
class DatabaseErrorTest extends TestCase {
	public function testConstructorWithNoArguments(): void {
		$error = new DatabaseError([]);

		$this->assertInstanceOf(DatabaseError::class, $error);
	}

	public function testConstructorWithEmptyArray(): void {
		$error = new DatabaseError([]);

		$this->assertInstanceOf(DatabaseError::class, $error);
	}

	public function testConstructorWithArray(): void {
		$settings = ['TXT_queryFailed' => 'Custom error message'];
		$error = new DatabaseError($settings);

		$this->assertInstanceOf(DatabaseError::class, $error);
	}

	public function testConstructorWithDBErrorSettings(): void {
		$settings = new DatabaseErrorSettings();
		$error = new DatabaseError(['test' => 'value']);

		$this->assertInstanceOf(DatabaseError::class, $error);
	}

	public function testDBErrorSettingsDefaults(): void {
		$settings = new DatabaseErrorSettings();

		$this->assertIsObject($settings);
	}
}
