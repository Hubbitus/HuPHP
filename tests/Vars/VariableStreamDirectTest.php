<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\VariableStream;
use PHPUnit\Framework\TestCase;

/**
* Direct method tests for VariableStream class.
*
* This test class exists to work around PHPUnit/Xdebug coverage limitation:
* When using stream_wrapper_register(), methods are called internally by PHP's
* stream layer, and Xdebug cannot track them as "covered" lines.
*
* These tests call methods directly to ensure measurable code coverage.
*
* For functional stream wrapper tests, see VariableStreamTest which uses
* standard file operations (fopen, fread, fwrite, etc.).
*
* @covers \Hubbitus\HuPHP\Vars\VariableStream
* @see VariableStreamTest For functional stream wrapper tests
* @see \Hubbitus\HuPHP\Vars\VariableStream The class under test
**/
class VariableStreamDirectTest extends TestCase {
	private VariableStream $stream;

	protected function setUp(): void {
		// Register stream wrapper if not already registered
		// VariableStream is already registered by the autoloader in VariableStream.php
		// so we just get the existing wrapper instance
		$this->stream = new VariableStream();
	}

	public function testStreamOpen(): void {
		$GLOBALS['testVarOpen'] = 'test content';
		$result = $this->stream->stream_open('var://testVarOpen', 'r+', 0, $opened_path);

		$this->assertTrue($result);
		$this->assertEquals('testVarOpen', $this->stream->varname);
	}

	public function testStreamOpenWithUnsetVariable(): void {
		// Test when variable is not set - should be initialized to ''
		unset($GLOBALS['testVarOpen3']);
		$result = $this->stream->stream_open('var://testVarOpen3', 'r', 0, $opened_path);

		$this->assertTrue($result);
		$this->assertEquals('testVarOpen3', $this->stream->varname);
		$this->assertEquals('', $GLOBALS['testVarOpen3']);
	}

	public function testStreamOpenWithDifferentPath(): void {
		$GLOBALS['testVarOpen2'] = 'test';
		$result = $this->stream->stream_open('var://testVarOpen2', 'w', 0, $opened_path);

		$this->assertTrue($result);
		$this->assertEquals('testVarOpen2', $this->stream->varname);
		// Verify that 'w' mode clears the variable
		$this->assertEquals('', $GLOBALS['testVarOpen2']);
	}

	public function testStreamRead(): void {
		$GLOBALS['testVarRead'] = 'Hello World';
		$this->stream->varname = 'testVarRead';
		$this->stream->position = 0;

		$data = $this->stream->stream_read(5);

		$this->assertEquals('Hello', $data);
		$this->assertEquals(5, $this->stream->position);
	}

	public function testStreamReadRemaining(): void {
		$GLOBALS['testVarReadRem'] = 'Test';
		$this->stream->varname = 'testVarReadRem';
		$this->stream->position = 2;

		$data = $this->stream->stream_read(2);

		$this->assertEquals('st', $data);
		$this->assertEquals(4, $this->stream->position);
	}

	public function testStreamReadBeyondLength(): void {
		$GLOBALS['testVarReadBeyond'] = 'Hi';
		$this->stream->varname = 'testVarReadBeyond';
		$this->stream->position = 1;

		$data = $this->stream->stream_read(10);

		$this->assertEquals('i', $data);
		$this->assertEquals(2, $this->stream->position);
	}

	public function testStreamWrite(): void {
		$GLOBALS['testVarWrite'] = 'Hello World';
		$this->stream->varname = 'testVarWrite';
		$this->stream->position = 6;

		$bytes = $this->stream->stream_write('PHP');

		$this->assertEquals(3, $bytes);
		$this->assertEquals('Hello PHPld', $GLOBALS['testVarWrite']);
		$this->assertEquals(9, $this->stream->position);
	}

	public function testStreamWriteAtBeginning(): void {
		$GLOBALS['testVarWriteBeg'] = 'World';
		$this->stream->varname = 'testVarWriteBeg';
		$this->stream->position = 0;

		$bytes = $this->stream->stream_write('Hello');

		$this->assertEquals(5, $bytes);
		$this->assertEquals('Hello', $GLOBALS['testVarWriteBeg']);
	}

	public function testStreamWriteExtends(): void {
		$GLOBALS['testVarWriteExt'] = 'Hi';
		$this->stream->varname = 'testVarWriteExt';
		$this->stream->position = 2;

		$bytes = $this->stream->stream_write('!');

		$this->assertEquals(1, $bytes);
		$this->assertEquals('Hi!', $GLOBALS['testVarWriteExt']);
	}

	public function testStreamTell(): void {
		$this->stream->position = 42;

		$position = $this->stream->stream_tell();

		$this->assertEquals(42, $position);
	}

	public function testStreamTellAtZero(): void {
		$this->stream->position = 0;

		$position = $this->stream->stream_tell();

		$this->assertEquals(0, $position);
	}

	public function testStreamEofAtBeginning(): void {
		$GLOBALS['testVarEof1'] = 'Test';
		$this->stream->varname = 'testVarEof1';
		$this->stream->position = 0;

		$eof = $this->stream->stream_eof();

		$this->assertFalse($eof);
	}

	public function testStreamEofAtEnd(): void {
		$GLOBALS['testVarEof2'] = 'Test';
		$this->stream->varname = 'testVarEof2';
		$this->stream->position = 4;

		$eof = $this->stream->stream_eof();

		$this->assertTrue($eof);
	}

	public function testStreamEofBeyondEnd(): void {
		$GLOBALS['testVarEof3'] = 'Test';
		$this->stream->varname = 'testVarEof3';
		$this->stream->position = 10;

		$eof = $this->stream->stream_eof();

		$this->assertTrue($eof);
	}

	public function testStreamEofWithEmptyVar(): void {
		$GLOBALS['testVarEof4'] = '';
		$this->stream->varname = 'testVarEof4';
		$this->stream->position = 0;

		$eof = $this->stream->stream_eof();

		$this->assertTrue($eof);
	}

	public function testStreamEofWithUnsetVar(): void {
		// Test when variable is not set in GLOBALS (null coalescing branch)
		unset($GLOBALS['testVarEof5']);
		$this->stream->varname = 'testVarEof5';
		$this->stream->position = 0;

		$eof = $this->stream->stream_eof();

		// When var is not set, it defaults to '', so position 0 >= strlen('') = 0
		$this->assertTrue($eof);
	}

	public function testStreamStat(): void {
		$stat = $this->stream->stream_stat();

		$this->assertIsArray($stat);
		$this->assertEmpty($stat);
	}

	public function testStreamSeekFromStart(): void {
		$GLOBALS['testVarSeek1'] = 'Hello World';
		$this->stream->varname = 'testVarSeek1';
		$this->stream->position = 0;

		$result = $this->stream->stream_seek(6, SEEK_SET);

		$this->assertTrue($result);
		$this->assertEquals(6, $this->stream->position);
	}

	public function testStreamSeekFromStartInvalid(): void {
		$GLOBALS['testVarSeek2'] = 'Test';
		$this->stream->varname = 'testVarSeek2';

		$result = $this->stream->stream_seek(10, SEEK_SET);

		$this->assertFalse($result);
	}

	public function testStreamSeekFromStartNegative(): void {
		$GLOBALS['testVarSeek3'] = 'Test';
		$this->stream->varname = 'testVarSeek3';

		$result = $this->stream->stream_seek(-1, SEEK_SET);

		$this->assertFalse($result);
	}

	public function testStreamSeekFromCurrent(): void {
		$GLOBALS['testVarSeek4'] = 'Hello World';
		$this->stream->varname = 'testVarSeek4';
		$this->stream->position = 5;

		$result = $this->stream->stream_seek(2, SEEK_CUR);

		$this->assertTrue($result);
		$this->assertEquals(7, $this->stream->position);
	}

	public function testStreamSeekFromCurrentNegative(): void {
		// Note: Legacy code has a bug - SEEK_CUR with negative offset returns false
		// This test documents the current (buggy) behavior
		$GLOBALS['testVarSeek5'] = 'Hello World';
		$this->stream->varname = 'testVarSeek5';
		$this->stream->position = 5;

		$result = $this->stream->stream_seek(-2, SEEK_CUR);

		// This is the current behavior - negative offsets not supported for SEEK_CUR
		$this->assertFalse($result);
	}

	public function testStreamSeekFromCurrentInvalid(): void {
		$GLOBALS['testVarSeek6'] = 'Test';
		$this->stream->varname = 'testVarSeek6';
		$this->stream->position = 2;

		$result = $this->stream->stream_seek(-10, SEEK_CUR);

		$this->assertFalse($result);
	}

	public function testStreamSeekFromEnd(): void {
		$GLOBALS['testVarSeek7'] = 'Hello World';
		$this->stream->varname = 'testVarSeek7';

		$result = $this->stream->stream_seek(-5, SEEK_END);

		$this->assertTrue($result);
		$this->assertEquals(6, $this->stream->position);
	}

	public function testStreamSeekFromEndPositive(): void {
		$GLOBALS['testVarSeek8'] = 'Test';
		$this->stream->varname = 'testVarSeek8';

		$result = $this->stream->stream_seek(2, SEEK_END);

		$this->assertTrue($result);
		$this->assertEquals(6, $this->stream->position);
	}

	public function testStreamSeekFromEndInvalid(): void {
		$GLOBALS['testVarSeek9'] = 'Test';
		$this->stream->varname = 'testVarSeek9';

		$result = $this->stream->stream_seek(-10, SEEK_END);

		$this->assertFalse($result);
	}

	public function testStreamSeekInvalidWhence(): void {
		$GLOBALS['testVarSeek10'] = 'Test';
		$this->stream->varname = 'testVarSeek10';

		$result = $this->stream->stream_seek(0, 999);

		$this->assertFalse($result);
	}

	public function testFullReadWriteCycle(): void {
		$GLOBALS['testVarCycle'] = 'Initial';
		$this->stream->varname = 'testVarCycle';
		$this->stream->position = 0;

		// Read first 3 chars
		$read = $this->stream->stream_read(3);
		$this->assertEquals('Ini', $read);

		// Write at position 3
		$bytes = $this->stream->stream_write('tial');
		$this->assertEquals(4, $bytes);

		// Seek back and read
		$this->stream->stream_seek(0, SEEK_SET);
		$read = $this->stream->stream_read(7);
		$this->assertEquals('Initial', $read);
	}

	public function testPositionAfterWrite(): void {
		$GLOBALS['testVarPos'] = '';
		$this->stream->varname = 'testVarPos';
		$this->stream->position = 0;

		$this->stream->stream_write('ABC');
		$this->assertEquals(3, $this->stream->position);

		$this->stream->stream_write('DEF');
		$this->assertEquals(6, $this->stream->position);
	}

	public function testStreamReadWithMultibyteString(): void {
		$GLOBALS['testVarMb'] = 'Привет';
		$this->stream->varname = 'testVarMb';
		$this->stream->position = 0;

		// Read 3 bytes (not characters!)
		$data = $this->stream->stream_read(3);

		$this->assertEquals(3, \strlen($data));
	}
}
