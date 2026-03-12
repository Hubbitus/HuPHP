<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Filesystem;

use Hubbitus\HuPHP\Exceptions\Filesystem\FileNotExistsException;
use Hubbitus\HuPHP\Filesystem\FileInMemory;
use PHPUnit\Framework\TestCase;

/**
* Additional coverage for FileInMemory.
* @covers \Hubbitus\HuPHP\Filesystem\FileInMemory
**/
class FileInMemoryAdditionalTest extends TestCase {
	private string $testDir;
	private string $testFile;

	protected function setUp(): void {
		$this->testDir = \sys_get_temp_dir() . '/huphp_mem_' . \uniqid();
		\mkdir($this->testDir, 0777, true);
		$this->testFile = $this->testDir . '/test.txt';
		\file_put_contents($this->testFile, "LineA\nLineB\nLineC\n");
	}

	protected function tearDown(): void {
		// Remove any files created in test directory
		if (\is_dir($this->testDir)) {
			$files = \scandir($this->testDir);
			foreach ($files as $f) {
				if ($f !== '.' && $f !== '..') {
					@\unlink($this->testDir . '/' . $f);
				}
			}
			@\rmdir($this->testDir);
		}
	}

	public function testExplodeLinesDifferentEndings(): void {
		$windowsFile = $this->testDir . '/win.txt';
		\file_put_contents($windowsFile, "W1\r\nW2\r\nW3\r\n");
		$file = new FileInMemory($windowsFile);
		$file->loadContent();
		$lines = $file->getLines();
		// Windows line endings may produce extra empty entries; ensure at least 3 non‑empty lines
		$nonEmpty = \array_values(\array_filter($lines, fn($l) => $l !== ''));
		$this->assertGreaterThanOrEqual(3, \count($nonEmpty));
		$this->assertEquals('W1', $nonEmpty[0]);
		$this->assertEquals('W2', $nonEmpty[1]);
		$this->assertEquals('W3', $nonEmpty[2]);
	}

	public function testCacheLineOffsetsMidAndEnd(): void {
		$file = new FileInMemory($this->testFile);
		$file->loadContent();
		// offset 0 should be line 0 (already covered elsewhere)
		$midOffset = $file->getLineByOffset(6); // after "LineA\n" (5 chars + newline)
		$this->assertEquals(1, $midOffset);
		$endOffset = $file->getLineByOffset(12); // after second line
		$this->assertEquals(2, $endOffset);
		$offsetInfo = $file->getOffsetByLine(1);
		$this->assertIsArray($offsetInfo);
		$this->assertCount(2, $offsetInfo);
		$this->assertGreaterThanOrEqual(5, $offsetInfo[0]);
		$this->assertGreaterThan($offsetInfo[0], $offsetInfo[1]);
	}

	public function testIconvConversion(): void {
		// Create ISO-8859-1 encoded string with special char "é"
		$isoString = \iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "café\n");
		$isoFile = $this->testDir . '/iso.txt';
		\file_put_contents($isoFile, $isoString);
		$file = new FileInMemory($isoFile);
		$file->loadContent();
		$file->iconv('ISO-8859-1', 'UTF-8');
		$blob = $file->getBLOB();
		$this->assertStringContainsString('café', $blob);
		// clean up temporary iso file
		\unlink($isoFile);
	}

	public function testWriteContentToNonExistingDirectoryThrowsException(): void {
		$file = new FileInMemory('/nonexistent/dir/file.txt');
		$file->setContentFromString('data');
		$this->expectException(FileNotExistsException::class);
		$file->writeContent();
	}
}
