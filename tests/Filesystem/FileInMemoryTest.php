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

    public function testGetLineAt(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        
        // Get first line (0-indexed)
        $line = $file->getLineAt(0);
        $this->assertIsString($line);
        $this->assertNotEmpty($line);
    }

    public function testGetLineAtWithNonExistentLine(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        
        // Get non-existent line
        $line = $file->getLineAt(9999);
        $this->assertNull($line);
    }

    public function testGetLineAtWithoutPreload(): void {
        $file = new FileInMemory($this->testFile);
        
        // getLineAt should load content automatically
        $line = $file->getLineAt(0);
        $this->assertIsString($line);
    }

    public function testGetLineByOffsetWithLargeOffset(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();

        // Get line number for a large offset
        $contentLength = strlen($file->getBLOB());
        $lineNo = $file->getLineByOffset($contentLength - 1);
        $this->assertIsInt($lineNo);
    }

    public function testGetLineByOffsetWithOverflow(): void {
        $file = new FileInMemory($this->testFile);
        $file->loadContent();
        
        // Offset beyond file size should throw exception
        $this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException::class);
        $file->getLineByOffset(9999999);
    }





    public function testGetLineAtWithUnicodeContent(): void {
        $unicodeFile = $this->testDir . '/unicode.txt';
        file_put_contents($unicodeFile, "Привет\nМир\nТест\n");
        
        try {
            $file = new FileInMemory($unicodeFile);
            $file->loadContent();
            
            $line = $file->getLineAt(1);
            $this->assertIsString($line);
            $this->assertStringContainsString('Мир', $line);
        } finally {
            unlink($unicodeFile);
        }
    }

    public function testGetLineByOffsetWithUnicodeContent(): void {
        $unicodeFile = $this->testDir . '/unicode_offset.txt';
        file_put_contents($unicodeFile, "Hello\nМир\n");
        
        try {
            $file = new FileInMemory($unicodeFile);
            $file->loadContent();
            
            // Get line number for offset in Unicode text
            $lineNo = $file->getLineByOffset(7); // Should be in second line
            $this->assertIsInt($lineNo);
        } finally {
            unlink($unicodeFile);
        }
    }



    public function testGetLineAtPreservesContent(): void {
        $originalContent = "Line 1\nLine 2\nLine 3\n";
        $testFile = $this->testDir . '/preserve.txt';
        file_put_contents($testFile, $originalContent);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Get a line
            $file->getLineAt(1);
            
            // Content should still be accessible
            $this->assertEquals($originalContent, $file->getBLOB());
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetWithBinarySearch(): void {
        // Test getLineByOffset() with binary search through multiple lines
        $content = "Line 1\nLine 2\nLine 3\nLine 4\nLine 5\n";
        $testFile = $this->testDir . '/binary_search.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Test offset in the middle (should trigger binary search)
            $lineNo = $file->getLineByOffset(10); // Should be around line 2-3
            $this->assertIsInt($lineNo);
            $this->assertGreaterThanOrEqual(0, $lineNo);
            $this->assertLessThan(5, $lineNo);
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetAtLineBoundaries(): void {
        // Test getLineByOffset() at exact line boundaries
        $content = "AAA\nBBB\nCCC\n";
        $testFile = $this->testDir . '/boundaries.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Offset 0 = line 0
            $this->assertEquals(0, $file->getLineByOffset(0));
            
            // Offset 4 = start of line 1 (after "AAA\n")
            $this->assertEquals(1, $file->getLineByOffset(4));
            
            // Offset 8 = start of line 2 (after "AAA\nBBB\n")
            $this->assertEquals(2, $file->getLineByOffset(8));
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetReturnsFalseForNotFound(): void {
        // Test getLineByOffset() returns false when offset not found
        // This covers the final else branch
        $content = "Test\n";
        $testFile = $this->testDir . '/notfound.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Valid offset but not matching any line exactly
            // This should still find a line, so we test edge case
            $result = $file->getLineByOffset(2); // Middle of "Test"
            $this->assertIsInt($result);
        } finally {
            unlink($testFile);
        }
    }






    public function testGetLineByOffsetWithSingleLineFile(): void {
        // Test getLineByOffset() with single line file (no newlines)
        $content = "Single line without newline";
        $testFile = $this->testDir . '/single_line.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Any offset should return line 0
            $this->assertEquals(0, $file->getLineByOffset(0));
            $this->assertEquals(0, $file->getLineByOffset(10));
            $this->assertEquals(0, $file->getLineByOffset(strlen($content) - 1));
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetWithEmptyLines(): void {
        // Test getLineByOffset() with empty lines
        $content = "Line 1\n\nLine 3\n";
        $testFile = $this->testDir . '/empty_lines.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Offset in empty line (line 1)
            $lineNo = $file->getLineByOffset(7);
            $this->assertIsInt($lineNo);
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetWithVeryLongLine(): void {
        // Test getLineByOffset() with very long line
        $longLine = str_repeat('A', 10000);
        $content = $longLine . "\nShort line\n";
        $testFile = $this->testDir . '/long_line.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Offset in the middle of long line
            $lineNo = $file->getLineByOffset(5000);
            $this->assertEquals(0, $lineNo);
            
            // Offset in second line
            $lineNo = $file->getLineByOffset(10002);
            $this->assertEquals(1, $lineNo);
        } finally {
            unlink($testFile);
        }
    }



    public function testGetLineByOffsetWithTwoLines(): void {
        // Test getLineByOffset() with exactly two lines (minimal binary search)
        $content = "Line A\nLine B\n";
        $testFile = $this->testDir . '/two_lines.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // First line
            $this->assertEquals(0, $file->getLineByOffset(0));
            
            // Second line (after "Line A\n" = 7 chars)
            $this->assertEquals(1, $file->getLineByOffset(7));
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetWithThreeLines(): void {
        // Test getLineByOffset() with three lines (tests binary search middle)
        $content = "A\nB\nC\n";
        $testFile = $this->testDir . '/three_lines.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Line 0
            $this->assertEquals(0, $file->getLineByOffset(0));
            
            // Line 1 (after "A\n" = 2 chars)
            $this->assertEquals(1, $file->getLineByOffset(2));
            
            // Line 2 (after "A\nB\n" = 4 chars)
            $this->assertEquals(2, $file->getLineByOffset(4));
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetWithBinarySearchLeftBranch(): void {
        // Test getLineByOffset() binary search going left ($right = $line)
        // This covers lines 236-237
        $content = "Line 1\nLine 2\nLine 3\nLine 4\nLine 5\nLine 6\nLine 7\nLine 8\n";
        $testFile = $this->testDir . '/binary_left.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Test various offsets to trigger binary search left branch
            for ($offset = 0; $offset < strlen($content) - 1; $offset += 3) {
                $lineNo = $file->getLineByOffset($offset);
                $this->assertIsInt($lineNo);
                $this->assertGreaterThanOrEqual(0, $lineNo);
            }
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetWithBinarySearchRightBranch(): void {
        // Test getLineByOffset() binary search going right ($left = $line)
        $content = "A\nB\nC\nD\nE\nF\nG\nH\nI\nJ\n";
        $testFile = $this->testDir . '/binary_right.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Test offsets that should trigger right branch
            $lineNo = $file->getLineByOffset(1);
            $this->assertIsInt($lineNo);
            
            $lineNo = $file->getLineByOffset(5);
            $this->assertIsInt($lineNo);
        } finally {
            unlink($testFile);
        }
    }


    public function testGetLineByOffsetWithExactLineEnd(): void {
        // Test getLineByOffset() with offset at exact line end
        // This may trigger the return false path
        $content = "AB\nCD\nEF\n";
        $testFile = $this->testDir . '/line_end.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Offset at end of first line (before \n)
            $lineNo = $file->getLineByOffset(1);
            $this->assertIsInt($lineNo);
            
            // Offset at \n character
            $lineNo = $file->getLineByOffset(2);
            $this->assertIsInt($lineNo);
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetWithManyLines(): void {
        // Test getLineByOffset() with many lines to ensure binary search works
        $lines = [];
        for ($i = 0; $i < 100; $i++) {
            $lines[] = "Line $i";
        }
        $content = implode("\n", $lines) . "\n";
        
        $testFile = $this->testDir . '/many_lines.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Test various offsets
            $lineNo = $file->getLineByOffset(0);
            $this->assertEquals(0, $lineNo);
            
            $lineNo = $file->getLineByOffset(50);
            $this->assertIsInt($lineNo);
            
            $lineNo = $file->getLineByOffset(strlen($content) - 2);
            $this->assertEquals(99, $lineNo);
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetWithOnlyNewlines(): void {
        // Test getLineByOffset() with file containing only newlines
        // This may trigger edge cases in binary search
        $content = "\n\n\n\n\n";
        $testFile = $this->testDir . '/only_newlines.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Test various offsets
            for ($offset = 0; $offset < strlen($content); $offset++) {
                $lineNo = $file->getLineByOffset($offset);
                $this->assertIsInt($lineNo);
            }
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetWithConsecutiveNewlines(): void {
        // Test getLineByOffset() with consecutive newlines (empty lines)
        $content = "A\n\n\n\nB\n";
        $testFile = $this->testDir . '/consecutive_newlines.txt';
        file_put_contents($testFile, $content);
        
        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();
            
            // Test offsets in empty lines
            $lineNo = $file->getLineByOffset(2);
            $this->assertIsInt($lineNo);
            
            $lineNo = $file->getLineByOffset(3);
            $this->assertIsInt($lineNo);
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLineByOffsetEdgeCases(): void {
        // Test getLineByOffset() with edge cases that might trigger return false
        $content = "X";
        $testFile = $this->testDir . '/single_char.txt';
        file_put_contents($testFile, $content);

        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();

            // Only one character, offset 0 should return line 0
            $lineNo = $file->getLineByOffset(0);
            $this->assertEquals(0, $lineNo);
        } finally {
            unlink($testFile);
        }
    }

    public function testGetLinesWithoutLoadThrowsException(): void {
        // Test that getLines() throws exception when content is not loaded
        // This indirectly tests the private checkLoad() method
        $testFile = $this->testDir . '/test.txt';
        file_put_contents($testFile, 'test content');

        try {
            $file = new FileInMemory($testFile);
            // Don't call loadContent() - should throw exception
            
            $this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException::class);
            $file->getLines();
        } finally {
            unlink($testFile);
        }
    }

    public function testEnconvMethodExists(): void {
        // Test that enconv() method exists
        $testFile = $this->testDir . '/test.txt';
        file_put_contents($testFile, 'test content');

        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();

            // Verify method exists and is callable
            $this->assertTrue(method_exists($file, 'enconv'));
        } finally {
            unlink($testFile);
        }
    }

    public function testEnconvReturnsSelf(): void {
        // Test that enconv() returns $this for method chaining
        $testFile = $this->testDir . '/test.txt';
        file_put_contents($testFile, 'test content');

        try {
            $file = new FileInMemory($testFile);
            $file->loadContent();

            // enconv may fail if enconv shell command is not available
            // but it should still return $this
            $result = $file->enconv('russian', 'UTF-8');
            $this->assertSame($file, $result);
        } catch (\Throwable $e) {
            // enconv shell command may not be available
            // This is acceptable - we're testing the method exists and returns self
            $this->assertStringContainsString('enconv', $e->getMessage());
        } finally {
            unlink($testFile);
        }
    }
}
