<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use PHPUnit\Framework\TestCase;

/**
* Functional tests for VariableStream class using stream wrapper protocol.
*
* These tests verify the stream wrapper works correctly through standard
* file operations (fopen, fread, fwrite, etc.).
*
* Note: Code coverage for these tests is not measurable due to PHPUnit/Xdebug
* limitation with stream_wrapper_register(). Methods are called internally by
* PHP's stream layer and Xdebug cannot track them as covered lines.
*
* For measurable coverage, see VariableStreamDirectTest which calls methods directly.
*
* @covers \Hubbitus\HuPHP\Vars\VariableStream
* @see VariableStreamDirectTest For direct method tests with measurable coverage
* @see \Hubbitus\HuPHP\Vars\VariableStream The class under test
**/
class VariableStreamTest extends TestCase {
    protected function setUp(): void {
        global $testVar;
        $testVar = '';
    }

    public function testStreamWrapperIsRegistered(): void {
        $wrappers = stream_get_wrappers();
        $this->assertContains('var', $wrappers);
    }

    public function testStreamOpen(): void {
        global $testVar;
        $testVar = 'test content';

        $fp = fopen('var://testVar', 'r');

        $this->assertIsResource($fp);
        fclose($fp);
    }

    public function testStreamRead(): void {
        global $testVar;
        $testVar = 'Hello World';

        $fp = fopen('var://testVar', 'r');
        $data = fread($fp, 5);

        $this->assertEquals('Hello', $data);
        fclose($fp);
    }

    public function testStreamReadPartial(): void {
        global $testVar;
        $testVar = 'Test';

        $fp = fopen('var://testVar', 'r');
        $data1 = fread($fp, 2);
        $data2 = fread($fp, 2);

        $this->assertEquals('Te', $data1);
        $this->assertEquals('st', $data2);
        fclose($fp);
    }

    public function testStreamReadEmpty(): void {
        global $testVar;
        $testVar = '';

        $fp = fopen('var://testVar', 'r');
        $data = fread($fp, 10);

        $this->assertEquals('', $data);
        fclose($fp);
    }

    public function testStreamWrite(): void {
        global $testVar;
        $testVar = '';

        $fp = fopen('var://testVar', 'w');
        fwrite($fp, 'Hello');
        fclose($fp);

        $this->assertEquals('Hello', $testVar);
    }

    public function testStreamWriteAppend(): void {
        global $testVar;
        $testVar = 'Hello';

        $fp = fopen('var://testVar', 'a');
        fwrite($fp, ' World');
        fclose($fp);

        $this->assertEquals('Hello World', $testVar);
    }

    public function testStreamWriteOverwrite(): void {
        global $testVar;
        $testVar = 'Hello World';

        $fp = fopen('var://testVar', 'w');
        fwrite($fp, 'Test');
        fclose($fp);

        $this->assertEquals('Test', $testVar);
    }

    public function testStreamTell(): void {
        global $testVar;
        $testVar = 'Hello World';

        $fp = fopen('var://testVar', 'r');
        fread($fp, 5);
        $position = ftell($fp);

        $this->assertEquals(5, $position);
        fclose($fp);
    }

    public function testStreamTellAtEnd(): void {
        global $testVar;
        $testVar = 'Test';

        $fp = fopen('var://testVar', 'r');
        fread($fp, 4);
        $position = ftell($fp);

        $this->assertEquals(4, $position);
        fclose($fp);
    }

    public function testStreamEof(): void {
        global $testVar;
        $testVar = 'Test';

        $fp = fopen('var://testVar', 'r');

        $this->assertFalse(feof($fp));

        fread($fp, 4);
        $this->assertTrue(feof($fp));

        fclose($fp);
    }

    public function testStreamSeekFromStart(): void {
        global $testVar;
        $testVar = 'Hello World';

        $fp = fopen('var://testVar', 'r');
        fseek($fp, 6, SEEK_SET);
        $data = fread($fp, 5);

        $this->assertEquals('World', $data);
        fclose($fp);
    }

    public function testStreamSeekFromCurrent(): void {
        global $testVar;
        $testVar = 'Hello World';

        $fp = fopen('var://testVar', 'r');
        fread($fp, 6);
        fseek($fp, 2, SEEK_CUR);
        $data = fread($fp, 3);

        $this->assertEquals('rld', $data);
        fclose($fp);
    }

    public function testStreamSeekFromEnd(): void {
        global $testVar;
        $testVar = 'Hello World';

        $fp = fopen('var://testVar', 'r');
        fseek($fp, -5, SEEK_END);
        $data = fread($fp, 5);

        $this->assertEquals('World', $data);
        fclose($fp);
    }

    public function testStreamSeekInvalidOffset(): void {
        global $testVar;
        $testVar = 'Test';

        $fp = fopen('var://testVar', 'r');
        $result = fseek($fp, -10, SEEK_SET);

        $this->assertEquals(-1, $result);
        fclose($fp);
    }

    public function testStreamStat(): void {
        global $testVar;
        $testVar = 'Test';

        $fp = fopen('var://testVar', 'r');
        $stat = fstat($fp);

        $this->assertIsArray($stat);
        fclose($fp);
    }

    public function testStreamRewind(): void {
        global $testVar;
        $testVar = 'Hello World';

        $fp = fopen('var://testVar', 'r');
        fread($fp, 5);
        rewind($fp);
        $data = fread($fp, 5);

        $this->assertEquals('Hello', $data);
        fclose($fp);
    }

    public function testStreamReadWrite(): void {
        global $testVar;
        $testVar = 'Hello';

        $fp = fopen('var://testVar', 'r+');
        $data = fread($fp, 5);
        $this->assertEquals('Hello', $data);

        rewind($fp);
        fwrite($fp, 'World');
        fclose($fp);

        $this->assertEquals('World', $testVar);
    }

    public function testStreamMultipleWrites(): void {
        global $testVar;
        $testVar = '';

        $fp = fopen('var://testVar', 'w');
        fwrite($fp, "Line1\n");
        fwrite($fp, "Line2\n");
        fwrite($fp, "Line3\n");
        fclose($fp);

        $this->assertEquals("Line1\nLine2\nLine3\n", $testVar);
    }

    public function testStreamReadLineByLine(): void {
        global $testVar;
        $testVar = "Line1\nLine2\nLine3\n";

        $fp = fopen('var://testVar', 'r');
        $line1 = fgets($fp);
        $line2 = fgets($fp);
        $line3 = fgets($fp);
        fclose($fp);

        $this->assertEquals("Line1\n", $line1);
        $this->assertEquals("Line2\n", $line2);
        $this->assertEquals("Line3\n", $line3);
    }

    public function testStreamWithBinaryData(): void {
        global $testVar;
        $testVar = '';

        $fp = fopen('var://testVar', 'w');
        $binaryData = "\x00\x01\x02\x03";
        fwrite($fp, $binaryData);
        fclose($fp);

        $this->assertEquals($binaryData, $testVar);
    }

    public function testStreamWithUnicodeData(): void {
        global $testVar;
        $testVar = '';

        $fp = fopen('var://testVar', 'w');
        $unicodeData = 'Привет Мир';
        fwrite($fp, $unicodeData);
        fclose($fp);

        $this->assertEquals($unicodeData, $testVar);
    }

    public function testRegisterStreamWrapperReturnsTrueOnSuccess(): void {
        // Test the public static method directly
        // Unregister first to test registration
        stream_wrapper_unregister('var');
        
        $result = \Hubbitus\HuPHP\Vars\VariableStream::registerStreamWrapper();
        
        $this->assertTrue($result);
        $this->assertContains('var', stream_get_wrappers());
    }

    public function testRegisterStreamWrapperReturnsFalseWhenAlreadyRegistered(): void {
        // Test that calling registerStreamWrapper twice returns false
        // (wrapper already registered)
        
        // Ensure it's registered
        \Hubbitus\HuPHP\Vars\VariableStream::registerStreamWrapper();
        
        // Second call should return false
        $result = \Hubbitus\HuPHP\Vars\VariableStream::registerStreamWrapper();
        
        $this->assertFalse($result);
    }

    public function testRegisterStreamWrapperIsCalledOnClassLoad(): void {
        // Verify that the stream wrapper is automatically registered
        // when the class is loaded (via the code at the end of the file)
        $this->assertContains('var', stream_get_wrappers());
    }
}