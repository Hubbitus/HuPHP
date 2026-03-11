<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Filesystem;

use Hubbitus\HuPHP\Filesystem\FileBase;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileNotExistsException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Filesystem\FileBase
**/
class FileBaseUnknownErrorTest extends TestCase {
	private string $dir;
	private string $file;

	protected function setUp(): void {
		$this->dir = \sys_get_temp_dir() . '/huphp_no_write_' . \uniqid();
		\mkdir($this->dir, 0555, true); // read/execute only, no write
		$this->file = $this->dir . '/file.txt';
		// Do NOT create the file – we want a non-existent file to trigger FileNotExistsException
	}

	protected function tearDown(): void {
		// restore permissions to allow cleanup
		if (\file_exists($this->file)) {
			\chmod($this->file, 0644);
			\unlink($this->file);
		}
		if (\is_dir($this->dir)) {
			\chmod($this->dir, 0755);
			\rmdir($this->dir);
		}
	}

	public function testWriteContentUnknownErrorThrowsException(): void {
		$file = new FileBase($this->file);
		$file->setContentFromString('new data');
		$this->expectException(FileNotExistsException::class);
		$file->writeContent();
	}
}
