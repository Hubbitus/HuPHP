<?php
declare(strict_types=1);

/**
 * Test for FileRead class.
 */

namespace Hubbitus\HuPHP\Tests\Filesystem;

use Hubbitus\HuPHP\Filesystem\FileRead;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Filesystem\FileRead
 */
class FileReadTest extends TestCase {
    private string $testFile;
    private string $testDir;

    protected function setUp(): void {
        $this->testDir = sys_get_temp_dir() . '/hubbitus_test_' . uniqid();
        $this->testFile = $this->testDir . '/test_file.txt';

        if (!is_dir($this->testDir)) {
            mkdir($this->testDir, 0777, true);
        }

        file_put_contents($this->testFile, "Line 1\nLine 2\nLine 3\n");
    }

    protected function tearDown(): void {
        // Remove all test files recursively
        if (is_dir($this->testDir)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->testDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $fileinfo) {
                if ($fileinfo->isDir()) {
                    rmdir($fileinfo->getRealPath());
                } else {
                    unlink($fileinfo->getRealPath());
                }
            }

            rmdir($this->testDir);
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
        $this->assertEquals('Test content', file_get_contents($writeFile));
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
        file_put_contents($emptyFile, '');

        $file = new FileRead($emptyFile);
        $file->open('r');
        $line = $file->getline();
        $this->assertFalse($line);

        unlink($emptyFile);
    }

    public function testFileReadWithSingleLineFile(): void {
        $singleLineFile = $this->testDir . '/single.txt';
        file_put_contents($singleLineFile, 'Single line');

        $file = new FileRead($singleLineFile);
        $file->open('r');
        $line = $file->getline();
        $this->assertEquals('Single line', $line);

        unlink($singleLineFile);
    }

    public function testFileReadWithBinaryFile(): void {
        $binaryFile = $this->testDir . '/binary.bin';
        file_put_contents($binaryFile, "\x00\x01\x02\x03");

        $file = new FileRead($binaryFile);
        $file->open('rb');
        $tail = $file->getTail();
        $this->assertIsString($tail);

        unlink($binaryFile);
    }

    public function testFileReadWithUtf8File(): void {
        $utf8File = $this->testDir . '/utf8.txt';
        file_put_contents($utf8File, "Привет\nМир\n");

        $file = new FileRead($utf8File);
        $file->open('r');
        $line = $file->getline();
        $this->assertEquals("Привет\n", $line);

        unlink($utf8File);
    }

    public function testFileReadWithLongLine(): void {
        $longLineFile = $this->testDir . '/long.txt';
        $longLine = str_repeat('A', 10000) . "\n";
        file_put_contents($longLineFile, $longLine);

        $file = new FileRead($longLineFile);
        $file->open('r');
        $line = $file->getline();
        $this->assertEquals($longLine, $line);

        unlink($longLineFile);
    }

    public function testFileReadWithMultipleLines(): void {
        $multiLineFile = $this->testDir . '/multi.txt';
        $content = "Line 1\nLine 2\nLine 3\nLine 4\nLine 5\n";
        file_put_contents($multiLineFile, $content);

        $file = new FileRead($multiLineFile);
        $file->open('r');

        $lines = [];
        while (($line = $file->getline()) !== false) {
            $lines[] = $line;
        }

        $this->assertCount(5, $lines);

        unlink($multiLineFile);
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

        $this->assertTrue(method_exists($file, 'path'));
        $this->assertTrue(method_exists($file, 'rawPath'));
        $this->assertTrue(method_exists($file, 'isExists'));
        $this->assertTrue(method_exists($file, 'isReadable'));
        $this->assertTrue(method_exists($file, 'setContentFromString'));
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

        $this->assertEquals('Second', file_get_contents($writeFile));
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
        $this->assertEquals('Content', file_get_contents($writeFile));
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
        $serialized = serialize($file);
        $this->assertIsString($serialized);
    }

    public function testFileReadToString(): void {
        $file = new FileRead($this->testFile);
        $string = (string) $file;
        $this->assertIsString($string);
    }
}
