<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Filesystem;

use Hubbitus\HuPHP\Exceptions\Filesystem\FileNotReadableException;
use Hubbitus\HuPHP\Filesystem\FileBase;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Filesystem\FileBase
**/
class FileBaseAdditionalTest extends TestCase {
	private string $testFile;
	private string $testDir;

	protected function setUp(): void {
		$this->testDir = \sys_get_temp_dir() . '/huphp_test_' . \uniqid();
		$this->testFile = $this->testDir . '/test.txt';
		\mkdir($this->testDir, 0777, true);
		\file_put_contents($this->testFile, 'content');
	}

	protected function tearDown(): void {
		if (\file_exists($this->testFile)) {
			\unlink($this->testFile);
		}
		if (\is_dir($this->testDir)) {
			\rmdir($this->testDir);
		}
	}

	public function testWriteContentUnreadableFileThrowsException(): void {
		// make file unreadable
		\chmod($this->testFile, 0000);
		$file = new FileBase($this->testFile);
		$file->setContentFromString('new');
		$this->expectException(FileNotReadableException::class);
		$file->writeContent();
	}
}
