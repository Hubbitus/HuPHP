<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\System;

use Hubbitus\HuPHP\System\Process;
use Hubbitus\HuPHP\System\ProcessState;
use Hubbitus\HuPHP\Exceptions\ProcessException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\System\Process
* @covers \Hubbitus\HuPHP\System\ProcessState
**/
class ProcessTest extends TestCase {
    public function testClassHasConstants(): void {
        $this->assertEquals(0, Process::STDIN);
        $this->assertEquals(1, Process::STDOUT);
        $this->assertEquals(2, Process::STDERR);
    }

    public function testConstructorWithProcessState(): void {
        $state = new ProcessState();
        $process = new Process($state, true); // doNOTopen = true to prevent execution

        $this->assertInstanceOf(Process::class, $process);
    }

    public function testGetStateReturnsProcessState(): void {
        $state = new ProcessState();
        $process = new Process($state, true);

        $returnedState = $process->getState();

        $this->assertInstanceOf(ProcessState::class, $returnedState);
        $this->assertSame($state, $returnedState);
    }

    public function testSetStateChangesState(): void {
        $state1 = new ProcessState();
        $state2 = new ProcessState();
        $process = new Process($state1, true);

        $process->setState($state2);

        $this->assertSame($state2, $process->getState());
    }

    public function testExec(): void {
        $result = Process::exec('echo "test"');

        $this->assertIsString($result);
        $this->assertStringContainsString('test', $result);
    }

    public function testExecWithProcessState(): void {
        $state = new ProcessState();
        $state->CMD = 'echo "hello"';

        $result = Process::exec($state);

        $this->assertIsString($result);
        $this->assertStringContainsString('hello', $result);
    }

    public function testExecWithCwd(): void {
        $result = Process::exec('pwd', '/tmp');

        $this->assertIsString($result);
        $this->assertStringContainsString('/tmp', $result);
    }

    public function testExecWithEnv(): void {
        $result = Process::exec('echo $TEST_VAR', null, ['TEST_VAR' => 'custom_value']);

        $this->assertIsString($result);
        $this->assertStringContainsString('custom_value', $result);
    }

    public function testExecWithWriteData(): void {
        // Test with cat command which echoes input
        $result = Process::exec('cat', null, null, 'test input');

        $this->assertIsString($result);
        $this->assertStringContainsString('test input', $result);
    }

    public function testOpen(): void {
        // Test open() method explicitly
        $state = new ProcessState();
        $state->CMD = 'echo "test"';
        $process = new Process($state, true); // Don't open in constructor

        // Open manually
        $process->open();

        $this->assertNotNull($process);
    }

    public function testSetNonBlockingMode(): void {
        // Test setNonBlockingMode() method
        $state = new ProcessState();
        $state->CMD = 'cat';
        $process = new Process($state, true);
        $process->open();

        // Enable non-blocking mode
        $process->setNonBlockingMode(true, 100000);

        $this->assertTrue($state->nonBlockingMode);
        $this->assertEquals(100000, $state->nonBlockTimeout);
    }

    public function testSetNonBlockingModeDisabled(): void {
        // Test setNonBlockingMode() with disabled mode
        $state = new ProcessState();
        $state->CMD = 'echo "test"';
        $process = new Process($state, true);

        // Disable non-blocking mode (default) - don't call open to avoid blocking
        $process->setNonBlockingMode(false);

        $this->assertFalse($state->nonBlockingMode);
    }

    public function testWriteIn(): void {
        // Test writeIn() method
        $state = new ProcessState();
        $state->CMD = 'cat';
        $process = new Process($state, true);
        $process->open();

        // Write input
        $process->writeIn('test data');

        $this->assertEquals('test data', $state->writeData);

        // Cleanup
        $process->readOut();
        $process->readErr();
        $process->closeAll();
    }

    public function testWriteInWithNoWait(): void {
        // Test writeIn() with noWait parameter in non-blocking mode
        $state = new ProcessState();
        $state->CMD = 'cat';
        $process = new Process($state, true);
        $process->open();
        $process->setNonBlockingMode(true, 50000);

        // Write input with noWait
        $process->writeIn('test', true);

        $this->assertEquals('test', $state->writeData);

        // Cleanup - don't check exit code as cat may exit with non-zero in non-blocking mode
        $process->readOut();
        $process->readErr();
        try {
            $process->closeAll();
        } catch (ProcessException $e) {
            // Ignore exit code error in non-blocking mode
        }
    }

    public function testReadOut(): void {
        // Test readOut() method
        $state = new ProcessState();
        $state->CMD = 'echo "output"';
        $process = new Process($state);

        $process->writeIn();
        $process->readOut();

        $this->assertStringContainsString('output', $state->retVal);

        // Cleanup
        $process->readErr();
        $process->closeAll();
    }

    public function testReadErr(): void {
        // Test readErr() method
        $state = new ProcessState();
        $state->CMD = 'echo "error" >&2';
        $process = new Process($state);

        $process->writeIn();
        $process->readErr();

        $this->assertStringContainsString('error', $state->error);

        // Cleanup
        $process->readOut();
        $process->closeAll();
    }

    public function testCloseAll(): void {
        // Test closeAll() method
        $state = new ProcessState();
        $state->CMD = 'echo "test"';
        $process = new Process($state);

        $process->writeIn();
        $process->readOut();
        $process->readErr();

        // Close all should complete without error for successful command
        $process->closeAll();

        $this->assertEquals(0, $state->exit_code);
    }

    public function testCloseAllWithNonBlockingMode(): void {
        // Test closeAll() with non-blocking mode
        $state = new ProcessState();
        $state->CMD = 'echo "test"';
        $process = new Process($state, true);
        $process->open();
        $process->setNonBlockingMode(true, 50000);

        $process->writeIn();
        $process->readOut();
        $process->readErr();

        // Close all in non-blocking mode
        $process->closeAll();

        $this->assertEquals(0, $state->exit_code);
    }

    public function testCloseAllThrowsExceptionOnError(): void {
        // Test closeAll() throws exception with non-zero exit code
        $state = new ProcessState();
        $state->CMD = 'false'; // Command that exits with code 1
        $process = new Process($state);

        $process->writeIn();
        $process->readOut();
        $process->readErr();

        $this->expectException(ProcessException::class);
        $this->expectExceptionMessage('Ended with non 0 status');
        $process->closeAll();
    }

    public function testExecute(): void {
        // Test execute() method which reads output, error and closes
        $state = new ProcessState();
        $state->CMD = 'echo "result"';
        $process = new Process($state);

        $result = $process->execute();

        $this->assertStringContainsString('result', $result);
    }

    public function testExecuteThrowsExceptionOnError(): void {
        // Test execute() throws exception when there's error output
        $state = new ProcessState();
        $state->CMD = 'echo "error" >&2';
        $process = new Process($state);

        $this->expectException(ProcessException::class);
        $process->execute();
    }

    public function testExecuteReturnsResult(): void {
        // Test execute() returns result from state
        $state = new ProcessState();
        $state->CMD = 'echo "hello world"';
        $process = new Process($state);

        $result = $process->execute();

        $this->assertStringContainsString('hello world', $result);
    }

    public function testExecWithCustomCwdAndEnv(): void {
        // Test exec() with both cwd and env
        $result = Process::exec('pwd', '/tmp', ['TEST' => 'value']);

        $this->assertIsString($result);
        $this->assertStringContainsString('/tmp', $result);
    }

    public function testExecPreservesState(): void {
        // Test that exec() preserves state object
        $state = new ProcessState();
        $state->CMD = 'echo "test"';
        $state->setCwd('/tmp');

        $result = Process::exec($state);

        $this->assertIsString($result);
        $this->assertEquals('/tmp', $state->getCwd());
    }

    public function testProcessWithStdioConstants(): void {
        // Verify constants are accessible
        $this->assertEquals(0, Process::STDIN);
        $this->assertEquals(1, Process::STDOUT);
        $this->assertEquals(2, Process::STDERR);
    }

    public function testWriteInWithoutData(): void {
        // Test writeIn() without providing data (uses state->writeData)
        $state = new ProcessState();
        $state->CMD = 'cat';
        $state->writeData = 'predefined data';
        $process = new Process($state);

        $process->writeIn(); // No argument, should use predefined data

        $process->readOut();
        $process->readErr();
        $process->closeAll();

        $this->assertStringContainsString('predefined data', $state->retVal);
    }

    public function testSetNonBlockingModeWithDefaultTimeout(): void {
        // Test setNonBlockingMode() with default timeout
        $state = new ProcessState();
        $state->CMD = 'cat';
        $process = new Process($state, true);
        $process->open();

        $process->setNonBlockingMode(); // Default: true, 500000

        $this->assertTrue($state->nonBlockingMode);
        $this->assertEquals(500000, $state->nonBlockTimeout);
    }

    public function testOpenWithInvalidCommandDoesNotThrow(): void {
        // Test open() behavior with invalid command
        // Note: proc_open() may not throw exception in PHP 8+, just return false
        $state = new ProcessState();
        $state->CMD = '/nonexistent/command';
        $process = new Process($state, true);

        // Open should not create a valid resource but also not throw
        $process->open();

        // Verify process exists
        $this->assertNotNull($process);
    }

    public function testOpenThrowsExceptionWhenProcOpenFails(): void {
        // Test open() throws ProcessException when proc_open returns false
        // This covers the exception path in open() method
        \set_error_handler(function() { return true; }); // Suppress warnings

        try {
            $state = new ProcessState();
            $state->CMD = 'echo test';
            $process = new Process($state, true);

            // Modify descriptor spec to make proc_open fail
            $reflection = new \ReflectionClass($process);
            $descriptorSpecProp = $reflection->getProperty('descriptorSpec');
            $descriptorSpecProp->setAccessible(true);
            $descriptorSpecProp->setValue($process, [
                0 => ['invalid_type', 'r'] // Invalid descriptor type
            ]);

            $this->expectException(ProcessException::class);
            $this->expectExceptionMessage('Can\'t open process!');
            $process->open();
        } finally {
            \restore_error_handler();
        }
    }
}
