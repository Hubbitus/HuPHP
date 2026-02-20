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
 */
class ProcessTest extends TestCase
{
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

    public function testProcessStateExists(): void {
        $this->assertTrue(class_exists(ProcessState::class));
    }

    public function testProcessExceptionExists(): void {
        $this->assertTrue(class_exists(ProcessException::class));
    }
}
