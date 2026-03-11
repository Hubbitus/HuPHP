<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Filesystem;

use Hubbitus\HuPHP\Filesystem\FileRead;
use PHPUnit\Framework\TestCase;

/**
* Test for FileRead class.
* @covers \Hubbitus\HuPHP\Filesystem\FileRead
**/
class FileReadTest extends TestCase {
	private string $testFile;
	private string $testDir;

	protected function setUp(): void {
		$this->testDir = \sys_get_temp_dir() . '/hubbitus_test_' . \uniqid();
		$this->testFile = $this->testDir . '/test_file.txt';

		if (!\is_dir($this->testDir)) {
			\mkdir($this->testDir, 0777, true);
		}

		\file_put_contents($this->testFile, "Line 1\nLine 2\nLine 3\n");
	}

	protected function tearDown(): void {
		// Remove all test files recursively
		if (\is_dir($this->testDir)) {
			$files = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($this->testDir, \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::CHILD_FIRST
			);

			foreach ($files as $fileinfo) {
				if ($fileinfo->isDir()) {
					\rmdir($fileinfo->getRealPath());
				} else {
					\unlink($fileinfo->getRealPath());
				}
			}

			\rmdir($this->testDir);
		}
	}

	public function testClassInstantiation(): void {
		$file = new FileRead($this->testFile);
		$this->assertInstanceOf(FileRead::class, $file);
	}

	public function testClassExtendsFileBase(): void {
		$file = new FileRead($this->testFile);
		$this->assertInstanceOf('Hubbitus\\HuPHP\\Filesystem\\FileBase', $file);
	}

	public function testOpenMethod(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$this->assertNotNull($file);
	}

	public function testGetlineMethod(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$line = $file->getline();
		$this->assertIsString($line);
		$this->assertEquals("Line 1\n", $line);
	}

	public function testGetlineWithLength(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$line = $file->getline(5);
		$this->assertIsString($line);
		$this->assertEquals("Line", $line);
	}

	public function testLineNoMethod(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$this->assertEquals(0, $file->lineNo());

		$file->getline();
		$this->assertEquals(1, $file->lineNo());

		$file->getline();
		$this->assertEquals(2, $file->lineNo());
	}

	public function testGetTailMethod(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$tail = $file->getTail();
		$this->assertIsString($tail);
		$this->assertNotEmpty($tail);
	}

	public function testGetTailWithMaxLength(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$tail = $file->getTail(5);
		$this->assertIsString($tail);
		$this->assertEquals("Line ", $tail);
	}

	public function testGetTailWithOffset(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$tail = $file->getTail(-1, 7);
		$this->assertIsString($tail);
		$this->assertStringContainsString('Line 2', $tail);
	}

	public function testWriteContentMethod(): void {
		$writeFile = $this->testDir . '/write_test.txt';
		$file = new FileRead($writeFile);
		$file->setContentFromString('Test content');
		$file->writeContent();

		$this->assertFileExists($writeFile);
		$this->assertEquals('Test content', \file_get_contents($writeFile));
	}

	public function testPathMethod(): void {
		$file = new FileRead($this->testFile);
		$this->assertEquals($this->testFile, $file->path());
	}

	public function testRawPathMethod(): void {
		$file = new FileRead($this->testFile);
		$this->assertEquals($this->testFile, $file->rawPath());
	}

	public function testIsExistsMethod(): void {
		$file = new FileRead($this->testFile);
		$this->assertTrue($file->isExists());
	}

	public function testIsReadableMethod(): void {
		$file = new FileRead($this->testFile);
		$this->assertTrue($file->isReadable());
	}

	public function testOpenWithWriteMode(): void {
		$writeFile = $this->testDir . '/write_mode.txt';
		$file = new FileRead($writeFile);
		$file->open('w');
		$this->assertNotNull($file);
	}

	public function testOpenWithAppendMode(): void {
		$file = new FileRead($this->testFile);
		$file->open('a');
		$this->assertNotNull($file);
	}

	public function testGetlineMultipleTimes(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');

		$line1 = $file->getline();
		$line2 = $file->getline();
		$line3 = $file->getline();

		$this->assertEquals("Line 1\n", $line1);
		$this->assertEquals("Line 2\n", $line2);
		$this->assertEquals("Line 3\n", $line3);
	}

	public function testLineNoAfterMultipleGetlines(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');

		$file->getline();
		$file->getline();
		$file->getline();

		$this->assertEquals(3, $file->lineNo());
	}

	public function testGetTailAfterGetlines(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');

		$file->getline();
		$file->getline();

		$tail = $file->getTail();
		$this->assertIsString($tail);
		$this->assertStringContainsString('Line 3', $tail);
	}

	public function testFileReadWithEmptyFile(): void {
		$emptyFile = $this->testDir . '/empty.txt';
		\file_put_contents($emptyFile, '');

		$file = new FileRead($emptyFile);
		$file->open('r');
		$line = $file->getline();
		$this->assertFalse($line);

		\unlink($emptyFile);
	}

	public function testFileReadWithSingleLineFile(): void {
		$singleLineFile = $this->testDir . '/single.txt';
		\file_put_contents($singleLineFile, 'Single line');

		$file = new FileRead($singleLineFile);
		$file->open('r');
		$line = $file->getline();
		$this->assertEquals('Single line', $line);

		\unlink($singleLineFile);
	}

	public function testFileReadWithBinaryFile(): void {
		$binaryFile = $this->testDir . '/binary.bin';
		\file_put_contents($binaryFile, "\x00\x01\x02\x03");

		$file = new FileRead($binaryFile);
		$file->open('rb');
		$tail = $file->getTail();
		$this->assertIsString($tail);

		\unlink($binaryFile);
	}

	public function testFileReadWithUtf8File(): void {
		$utf8File = $this->testDir . '/utf8.txt';
		\file_put_contents($utf8File, "Привет\nМир\n");

		$file = new FileRead($utf8File);
		$file->open('r');
		$line = $file->getline();
		$this->assertEquals("Привет\n", $line);

		\unlink($utf8File);
	}

	public function testFileReadWithLongLine(): void {
		$longLineFile = $this->testDir . '/long.txt';
		$longLine = \str_repeat('A', 10000) . "\n";
		\file_put_contents($longLineFile, $longLine);

		$file = new FileRead($longLineFile);
		$file->open('r');
		$line = $file->getline();
		$this->assertEquals($longLine, $line);

		\unlink($longLineFile);
	}

	public function testFileReadWithMultipleLines(): void {
		$multiLineFile = $this->testDir . '/multi.txt';
		$content = "Line 1\nLine 2\nLine 3\nLine 4\nLine 5\n";
		\file_put_contents($multiLineFile, $content);

		$file = new FileRead($multiLineFile);
		$file->open('r');

		$lines = [];
		while (($line = $file->getline()) !== false) {
			$lines[] = $line;
		}

		$this->assertCount(5, $lines);

		\unlink($multiLineFile);
	}

	public function testFileReadGetTailReturnsRemainingContent(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');

		$file->getline();
		$tail = $file->getTail();

		$this->assertStringContainsString('Line 2', $tail);
		$this->assertStringContainsString('Line 3', $tail);
	}

	public function testFileReadOpenDoesNotThrowError(): void {
		$file = new FileRead($this->testFile);
		$this->expectNotToPerformAssertions();
		$file->open('r');
	}

	public function testFileReadWriteContentReturnsCount(): void {
		$writeFile = $this->testDir . '/write_count.txt';
		$file = new FileRead($writeFile);
		$file->setContentFromString('Test');
		$count = $file->writeContent();
		$this->assertIsInt($count);
		$this->assertGreaterThan(0, $count);
	}

	public function testFileReadInheritsMethodsFromFileBase(): void {
		$file = new FileRead($this->testFile);

		$this->assertTrue(\method_exists($file, 'path'));
		$this->assertTrue(\method_exists($file, 'rawPath'));
		$this->assertTrue(\method_exists($file, 'isExists'));
		$this->assertTrue(\method_exists($file, 'isReadable'));
		$this->assertTrue(\method_exists($file, 'setContentFromString'));
	}

	public function testFileReadConstructorWithNull(): void {
		$file = new FileRead();
		$this->assertInstanceOf(FileRead::class, $file);
	}

	public function testFileReadSetPath(): void {
		$file = new FileRead();
		$file->setPath($this->testFile);
		$this->assertEquals($this->testFile, $file->path());
	}

	public function testFileReadOpenWithIncludePath(): void {
		$file = new FileRead($this->testFile);
		$file->open('r', false);
		$this->assertNotNull($file);
	}

	public function testFileReadGetlineReturnsFalseAtEof(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');

		$file->getline();
		$file->getline();
		$file->getline();

		$line = $file->getline();
		$this->assertFalse($line);
	}

	public function testFileReadGetTailWithZeroMaxLength(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$tail = $file->getTail(0);
		$this->assertEquals('', $tail);
	}

	public function testFileReadGetTailWithNegativeOffset(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$tail = $file->getTail(-1, -5);
		$this->assertIsString($tail);
	}

	public function testFileReadWriteContentTwice(): void {
		$writeFile = $this->testDir . '/write_twice.txt';
		$file = new FileRead($writeFile);

		$file->setContentFromString('First');
		$file->writeContent();

		$file->setContentFromString('Second');
		$file->writeContent();

		$this->assertEquals('Second', \file_get_contents($writeFile));
	}

	public function testFileReadOpenReadOnly(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$tail = $file->getTail();
		$this->assertStringContainsString('Line 1', $tail);
	}

	public function testFileReadOpenReadWrite(): void {
		$file = new FileRead($this->testFile);
		$file->open('r+');
		$tail = $file->getTail();
		$this->assertStringContainsString('Line 1', $tail);
	}

	public function testFileReadLineNoIsInteger(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$this->assertIsInt($file->lineNo());
	}

	public function testFileReadGetlineWithZeroLength(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$line = $file->getline(0);
		$this->assertEquals('', $line);
	}

	public function testFileReadGetlineWithNegativeLength(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$line = $file->getline(-1);
		$this->assertIsString($line);
	}

	public function testFileReadGetTailDefaultParameters(): void {
		$file = new FileRead($this->testFile);
		$file->open('r');
		$tail = $file->getTail();
		$this->assertIsString($tail);
		$this->assertNotEmpty($tail);
	}

	public function testFileReadOpenCreatesFileInWriteMode(): void {
		$newFile = $this->testDir . '/new.txt';
		$file = new FileRead($newFile);
		$file->open('w');
		$this->assertFileExists($newFile);
	}

	public function testFileReadWriteContentAfterOpen(): void {
		$writeFile = $this->testDir . '/write_after_open.txt';
		$file = new FileRead($writeFile);
		$file->open('w');
		$file->setContentFromString('Content');
		$file->writeContent();
		$this->assertEquals('Content', \file_get_contents($writeFile));
	}

	public function testFileReadMultipleInstances(): void {
		$file1 = new FileRead($this->testFile);
		$file2 = new FileRead($this->testFile);

		$file1->open('r');
		$file2->open('r');

		$this->assertNotNull($file1);
		$this->assertNotNull($file2);
	}

	public function testFileReadClone(): void {
		$file1 = new FileRead($this->testFile);
		$file2 = clone $file1;

		$this->assertEquals($file1->path(), $file2->path());
	}

	public function testFileReadSerialization(): void {
		$file = new FileRead($this->testFile);
		$serialized = \serialize($file);
		$this->assertIsString($serialized);
	}

	public function testFileReadToString(): void {
		$file = new FileRead($this->testFile);
		$string = (string) $file;
		$this->assertIsString($string);
	}

	public function testOpenWithZContext(): void {
		// Test open() with stream context (zContext parameter)
		$context = \stream_context_create([
			'file' => ['cache' => false]
		]);

		$file = new FileRead($this->testFile);
		$file->open('r', false, $context);
		$this->assertNotNull($file);

		$tail = $file->getTail();
		$this->assertIsString($tail);
		$this->assertNotEmpty($tail);
	}

	public function testWriteContentWithFileDescriptor(): void {
		// Test writeContent() when file descriptor is open
		$writeFile = $this->testDir . '/fd_write.txt';
		$file = new FileRead($writeFile);

		// Open file descriptor for writing
		$file->open('w');

		// Set content and write via descriptor
		$file->setContentFromString('Content via FD');
		$bytesWritten = $file->writeContent();

		$this->assertIsInt($bytesWritten);
		$this->assertGreaterThan(0, $bytesWritten);
		$this->assertEquals('Content via FD', \file_get_contents($writeFile));
	}

	public function testWriteContentWithFlags(): void {
		// Test writeContent() with FILE_USE_INCLUDE_PATH flag
		$writeFile = $this->testDir . '/flags_write.txt';
		$file = new FileRead($writeFile);
		$file->setContentFromString('Content with flags');

		$bytesWritten = $file->writeContent(FILE_USE_INCLUDE_PATH);

		$this->assertIsInt($bytesWritten);
		$this->assertGreaterThan(0, $bytesWritten);
		$this->assertEquals('Content with flags', \file_get_contents($writeFile));
	}

	public function testWriteContentWithResourceContext(): void {
		// Test writeContent() with resource context
		$context = \stream_context_create([
			'file' => ['cache' => false]
		]);

		$writeFile = $this->testDir . '/context_write.txt';
		$file = new FileRead($writeFile);
		$file->setContentFromString('Content with context');

		$bytesWritten = $file->writeContent(null, $context);

		$this->assertIsInt($bytesWritten);
		$this->assertGreaterThan(0, $bytesWritten);
		$this->assertEquals('Content with context', \file_get_contents($writeFile));
	}

	public function testOpenWithWriteModeAndZContext(): void {
		// Test open() with write mode and zContext
		$context = \stream_context_create([
			'file' => ['cache' => false]
		]);

		$writeFile = $this->testDir . '/w_context.txt';
		$file = new FileRead($writeFile);
		$file->open('w', false, $context);

		$this->assertNotNull($file);

		// Write and verify
		$file->setContentFromString('Written with context');
		$file->writeContent();
		$this->assertEquals('Written with context', \file_get_contents($writeFile));
	}

	public function testOpenWithAppendModeAndZContext(): void {
		// Test open() with append mode and zContext
		$context = \stream_context_create([
			'file' => ['cache' => false]
		]);

		$file = new FileRead($this->testFile);
		$file->open('a', false, $context);

		$this->assertNotNull($file);
	}

	public function testWriteContentReturnsCorrectByteCount(): void {
		// Test that writeContent returns exact byte count
		$writeFile = $this->testDir . '/byte_count.txt';
		$content = 'Test content 123';
		$file = new FileRead($writeFile);
		$file->setContentFromString($content);

		$bytesWritten = $file->writeContent();

		$this->assertEquals(\strlen($content), $bytesWritten);
	}

	public function testOpenWithExclusiveMode(): void {
		// Test open() with exclusive creation mode 'x'
		$newFile = $this->testDir . '/exclusive.txt';
		$file = new FileRead($newFile);
		$file->open('x');

		$this->assertNotNull($file);
		$this->assertFileExists($newFile);
	}

	public function testOpenWithReadWriteMode(): void {
		// Test open() with r+ mode (read-write)
		$file = new FileRead($this->testFile);
		$file->open('r+');

		$tail = $file->getTail();
		$this->assertIsString($tail);
		$this->assertNotEmpty($tail);
	}

	public function testOpenWithWriteReadMode(): void {
		// Test open() with w+ mode (write-read, truncate)
		$writeFile = $this->testDir . '/wr_mode.txt';
		$file = new FileRead($writeFile);
		$file->open('w+');

		$file->setContentFromString('W+ mode content');
		$file->writeContent();

		$tail = $file->getTail();
		$this->assertEquals('W+ mode content', $tail);
	}

	public function testOpenWithAppendReadMode(): void {
		// Test open() with a+ mode (append-read)
		$file = new FileRead($this->testFile);
		$file->open('a+');

		$tail = $file->getTail();
		$this->assertIsString($tail);
	}

	public function testWriteContentAfterMultipleWrites(): void {
		// Test multiple write operations
		$writeFile = $this->testDir . '/multi_write.txt';
		$file = new FileRead($writeFile);

		$file->setContentFromString('First');
		$file->writeContent();

		$file->setContentFromString('Second');
		$file->writeContent();

		$file->setContentFromString('Third');
		$file->writeContent();

		$this->assertEquals('Third', \file_get_contents($writeFile));
	}

	public function testOpenWithIncludePath(): void {
		// Test open() with use_include_path = true
		$file = new FileRead($this->testFile);
		$file->open('r', true);

		$tail = $file->getTail();
		$this->assertIsString($tail);
		$this->assertNotEmpty($tail);
	}

	public function testWriteContentWithEmptyContent(): void {
		// Test writeContent() with empty content
		$writeFile = $this->testDir . '/empty_content.txt';
		$file = new FileRead($writeFile);
		$file->setContentFromString('');

		$bytesWritten = $file->writeContent();

		$this->assertEquals(0, $bytesWritten);
		$this->assertFileExists($writeFile);
		$this->assertEquals('', \file_get_contents($writeFile));
	}

	public function testOpenWithBinaryWriteMode(): void {
		// Test open() with binary write mode 'wb'
		$writeFile = $this->testDir . '/binary_write.bin';
		$file = new FileRead($writeFile);
		$file->open('wb');

		$file->setContentFromString("\x00\x01\x02\x03");
		$file->writeContent();

		$this->assertEquals("\x00\x01\x02\x03", \file_get_contents($writeFile));
	}

	public function testOpenWithBinaryReadWriteMode(): void {
		// Test open() with binary read-write mode 'r+b'
		$file = new FileRead($this->testFile);
		$file->open('r+b');

		$tail = $file->getTail();
		$this->assertIsString($tail);
	}

	public function testWriteContentOverwritesExistingFile(): void {
		// Test that writeContent overwrites existing file content
		$writeFile = $this->testDir . '/overwrite.txt';
		\file_put_contents($writeFile, 'Original content');

		$file = new FileRead($writeFile);
		$file->setContentFromString('New content');
		$file->writeContent();

		$this->assertEquals('New content', \file_get_contents($writeFile));
	}

	public function testOpenTruncatesFileInWriteMode(): void {
		// Test that open('w') truncates existing file
		$writeFile = $this->testDir . '/truncate.txt';
		\file_put_contents($writeFile, 'Original content that should be truncated');

		$file = new FileRead($writeFile);
		$file->open('w');

		// File should be empty after open('w'), but we cannot read it because fd is write-only
		// Attempting to read from write-only fd should throw exception
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Cannot read from file opened in write mode');
		$file->getTail();
	}

	public function testWriteContentWithSpecialCharacters(): void {
		// Test writeContent() with special characters
		$writeFile = $this->testDir . '/special.txt';
		$content = "Special chars: \n\t\r\n\\\"'&<>";
		$file = new FileRead($writeFile);
		$file->setContentFromString($content);

		$file->writeContent();

		$this->assertEquals($content, \file_get_contents($writeFile));
	}

	public function testOpenWithExclusiveModeFailsIfExists(): void {
		// Test that open('x') fails if file already exists
		$existingFile = $this->testDir . '/existing.txt';
		\file_put_contents($existingFile, 'Existing');

		$file = new FileRead($existingFile);

		$this->expectException(\RuntimeException::class);
		$file->open('x');
	}

	public function testWriteContentWithReadOnlyFileDescriptor(): void {
		// Test writeContent() throws exception when file descriptor is read-only
		$file = new FileRead($this->testFile);
		$file->open('r'); // Open for reading only

		$file->setContentFromString('New content');

		// This should throw RuntimeException because fd is read-only
		// We need to catch the exception and reset _writePending to prevent __destruct notice
		$exceptionThrown = false;
		try {
			$file->writeContent();
		} catch (\RuntimeException $e) {
			$exceptionThrown = true;
			// Reset write pending flag to prevent __destruct write attempt
			$reflection = new \ReflectionClass($file);
			$property = $reflection->getProperty('_writePending');
			$property->setAccessible(true);
			$property->setValue($file, false);
		}

		$this->assertTrue($exceptionThrown, 'RuntimeException should be thrown');
	}

	public function testWriteContentWithInvalidPath(): void {
		// Test writeContent() throws exception when path is invalid
		$invalidPath = '/nonexistent/directory/file.txt';
		$file = new FileRead($invalidPath);
		$file->setContentFromString('Content');

		// This should throw RuntimeException because directory doesn't exist
		$this->expectException(\RuntimeException::class);
		$file->writeContent();
	}

	public function testOpenWithInvalidPathForWriting(): void {
		// Test open() throws exception when path is invalid for writing
		// Note: fopen() generates warning in PHP 8+, which is now suppressed with @
		$invalidPath = '/nonexistent/directory/file.txt';
		$file = new FileRead($invalidPath);

		// Should throw RuntimeException when fopen returns false
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Failed to open file for writing');
		$file->open('w');
	}

	public function testOpenWriteModeWithFalseResult(): void {
		// Test that open() throws RuntimeException when fopen returns false for write mode
		// This covers line 82 in FileRead.php
		// We use a path that will make fopen return false
		$file = new FileRead('/');

		// Opening root directory for write should fail
		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Failed to open file for writing');
		$file->open('w');
	}

	public function testWriteContentWithFwriteFailure(): void {
		// Test writeContent() throws RuntimeException when fwrite returns false
		// Register a custom stream wrapper that simulates write failure
		$wrapperName = 'failwrite';
		$wrapperClass = new class {
			public $context;
			public $position = 0;

			public function stream_open(string $path, string $mode, int $options, ?string &$opened_path): bool {
				return true;
			}

			public function stream_write(string $data): int|false {
				// Always return false to simulate write failure
				return false;
			}

			public function stream_eof(): bool {
				return true;
			}

			public function stream_stat(): array|false {
				return false;
			}

			public function stream_truncate(int $new_size): bool {
				// Support truncate but still fail on write
				return true;
			}

			public function stream_seek(int $offset, int $whence = SEEK_SET): bool {
				// Support seeking
				$this->position = $offset;
				return true;
			}

			public function stream_tell(): int {
				return $this->position;
			}
		};

		\stream_wrapper_register($wrapperName, $wrapperClass::class);

		try {
			$file = new FileRead($wrapperName . '://test.txt');
			$file->open('w');
			$file->setContentFromString('Test content');

			// This should throw RuntimeException because fwrite returns false
			$this->expectException(\RuntimeException::class);
			$this->expectExceptionMessage('Failed to write content to file');
			$file->writeContent();
		} finally {
			\stream_wrapper_unregister($wrapperName);
		}
	}
}
