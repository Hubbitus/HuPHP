<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\System;

use Hubbitus\HuPHP\System\OS;
use PHPUnit\Framework\TestCase;

class OSTest extends TestCase
{
	public function testGetOutType(): void
	{
		$result = OS::getOutType();
		$this->assertIsInt($result);
		$this->assertContains($result, [OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE]);
	}

	public function testPhpSapiName(): void
	{
		$result = OS::phpSapiName();
		$this->assertIsString($result);
		$this->assertContains($result, OS::$SAPIs);
	}

	public function testIsIncludeableWithExistingFile(): void
	{
		$existingFile = __FILE__;
		$result = OS::is_includeable($existingFile);
		$this->assertTrue($result);
	}

	public function testIsIncludeableWithNonExistingFile(): void
	{
		$nonExistingFile = '/path/to/non/existing/file.php';
		$result = OS::is_includeable($nonExistingFile);
		$this->assertFalse($result);
	}

	public function testIsPathAbsoluteUnix(): void
	{
		if ('WIN' != strtoupper(substr(PHP_OS, 0, 3))) {
			$this->assertTrue(OS::isPathAbsolute('/absolute/path'));
			$this->assertFalse(OS::isPathAbsolute('relative/path'));
		} else {
			$this->markTestSkipped('Not running on Unix-like system');
		}
	}

	public function testIsPathAbsoluteWindows(): void
	{
		if ('WIN' == strtoupper(substr(PHP_OS, 0, 3))) {
			$this->assertTrue(OS::isPathAbsolute('C:\\absolute\\path'));
			$this->assertFalse(OS::isPathAbsolute('relative\\path'));
		} else {
			$this->markTestSkipped('Not running on Windows');
		}
	}

	public function testIsPathAbsoluteWithStreamWrapper(): void
	{
		$this->assertTrue(OS::isPathAbsolute('file:///path/to/file'));
		$this->assertTrue(OS::isPathAbsolute('http://example.com'));
	}

	public function testConstants(): void
	{
		$this->assertEquals(1, OS::OUT_TYPE_BROWSER);
		$this->assertEquals(2, OS::OUT_TYPE_CONSOLE);
		$this->assertEquals(4, OS::OUT_TYPE_PRINT);
		$this->assertEquals(8, OS::OUT_TYPE_FILE);
		$this->assertEquals(16, OS::OUT_TYPE_WAP);
	}
}