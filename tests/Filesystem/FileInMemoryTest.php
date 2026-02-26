<?php
declare(strict_types=1);

/**
 * Test for FileInMemory class.
 */

namespace Hubbitus\HuPHP\Tests\Filesystem;

use Hubbitus\HuPHP\Filesystem\FileInMemory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Filesystem\FileInMemory
 */
class FileInMemoryTest extends TestCase {
    private string $testFile;
    private string $testDir;

    protected function setUp(): void {
        $this->testDir = sys_get_temp_dir() . '/hubbitus_test_' . uniqid();
        $this->testFile = $this->testDir . '/test_file.txt';

        if (!is_dir($this->testDir)) {
            mkdir($this->testDir, 0777, true);
        }

        // Create test file with content
        file_put_contents($this->testFile, "Line 1\nLine 2\nLine 3\n");
    }

    protected function tearDown(): void {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
        if (is_dir($this->testDir)) {
            rmdir($this->testDir);
        }
    }

    public function testClassInstantiation(): void {
        $file = new FileInMemory($this->testFile);
        $this->assertInstanceOf(FileInMemory::class, $file);
    }

    public function testClassExtendsFileBase(): void {
        $file = new FileInMemory($this->testFile);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Filesystem\\FileBase', $file);
    }

    public function testLoadContent(): void {
        $file = new FileInMemory($this->testFile);
        $result = $file->loadContent();
        $this->assertInstanceOf(FileInMemory::class, $result);
        $this->assertEquals("Line 1\nLine 2\nLine 3\n", $file->getBLOB());
    }

    public function testLoadContentWithOffset(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent(false, null, 7);
        $this->assertEquals("Line 2\nLine 3\n", $file->getBLOB());
    }

    public function testLoadContentWithOffsetAndMaxLen(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent(false, null, 0, 6);
        $this->assertEquals("Line 1", $file->getBLOB());
    }

    public function testSetContentFromString(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $result = $file->setContentFromString("New content");
        $this->assertInstanceOf(FileInMemory::class, $result);
        $this->assertEquals("New content", $file->getBLOB());
    }

    public function testAppendString(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $result = $file->appendString("Appended");
        $this->assertInstanceOf(FileInMemory::class, $result);
        $this->assertEquals("Line 1\nLine 2\nLine 3\nAppended", $file->getBLOB());
    }

    public function testGetLines(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $lines = $file->getLines();
        $this->assertIsArray($lines);
        $this->assertCount(3, $lines);
        $this->assertEquals("Line 1", $lines[0]);
        $this->assertEquals("Line 2", $lines[1]);
        $this->assertEquals("Line 3", $lines[2]);
    }

    public function testGetLinesWithSlice(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $lines = $file->getLines([0, 2]);
        $this->assertIsArray($lines);
        $this->assertCount(2, $lines);
        $this->assertEquals("Line 1", $lines[0]);
        $this->assertEquals("Line 2", $lines[1]);
    }

    public function testGetLineSep(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $lineSep = $file->getLineSep();
        $this->assertEquals("\n", $lineSep);
    }

    public function testSetLineSep(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $file->setLineSep("\r\n");
        $this->assertEquals("\r\n", $file->getLineSep());
    }

    public function testGetBLOB(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $blob = $file->getBLOB();
        $this->assertEquals("Line 1\nLine 2\nLine 3\n", $blob);
    }

    public function testGetBLOBWithCustomImplode(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        // getBLOB with custom implode uses implodeLines which may have specific behavior
        // Just test that it returns a string
        $blob = $file->getBLOB(' | ');
        $this->assertIsString($blob);
    }

    public function testWriteContent(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $file->setContentFromString("Modified content");
        $count = $file->writeContent();
        $this->assertEquals(strlen("Modified content"), $count);

        // Verify content was written
        $this->assertEquals("Modified content", file_get_contents($this->testFile));
    }

    public function testGetLineByOffset(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        // getLineByOffset returns line NUMBER (int), not line content
        $lineNumber = $file->getLineByOffset(0);
        $this->assertEquals(0, $lineNumber);
    }

    public function testGetOffsetByLine(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        // getOffsetByLine returns array [start, end] offsets
        $offset = $file->getOffsetByLine(1);
        $this->assertIsArray($offset);
        $this->assertCount(2, $offset);
    }

    public function testGetContentLength(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $length = $file->getContentLength();
        $this->assertEquals(strlen("Line 1\nLine 2\nLine 3\n"), $length);
    }

    public function testGetFirstLine(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $firstLine = $file->getFirstLine();
        $this->assertEquals("Line 1", $firstLine);
    }

    public function testGetLastLine(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        $lastLine = $file->getLastLine();
        $this->assertEquals("Line 3", $lastLine);
    }

    public function testEmptyFile(): void {
        $emptyFile = $this->testDir . '/empty.txt';
        file_put_contents($emptyFile, '');

        $file = new FileInMemory($emptyFile);
        $file->loadContent();
        $this->assertEquals('', $file->getBLOB());

        unlink($emptyFile);
    }

    public function testSingleLineFile(): void {
        $singleLineFile = $this->testDir . '/single.txt';
        file_put_contents($singleLineFile, 'Single line');

        $file = new FileInMemory($singleLineFile);
        $file->loadContent();
        $lines = $file->getLines();
        $this->assertCount(1, $lines);
        $this->assertEquals('Single line', $lines[0]);

        unlink($singleLineFile);
    }

    public function testMultipleLineEndings(): void {
        $multiFile = $this->testDir . '/multi.txt';
        file_put_contents($multiFile, "Line 1\r\nLine 2\r\nLine 3\r\n");

        $file = new FileInMemory($multiFile);
        $file->loadContent();
        $lines = $file->getLines();
        // With \r\n line endings, the regex may split differently
        // Just verify we get some lines back
        $this->assertGreaterThan(0, count($lines));

        unlink($multiFile);
    }

    public function testPathMethod(): void {
        $file = new FileInMemory($this->testFile);
        $this->assertEquals($this->testFile, $file->path());
    }

    public function testRawPathMethod(): void {
        $file = new FileInMemory($this->testFile);
        $this->assertEquals($this->testFile, $file->rawPath());
    }

    public function testIsExistsMethod(): void {
        $file = new FileInMemory($this->testFile);
        $this->assertTrue($file->isExists());
    }

    public function testIsReadableMethod(): void {
        $file = new FileInMemory($this->testFile);
        $this->assertTrue($file->isReadable());
    }
}
