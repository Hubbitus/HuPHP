<?php
declare(strict_types=1);

/**
 * Test for ProcessState class.
 */

namespace Hubbitus\HuPHP\Tests\System;

use Hubbitus\HuPHP\System\ProcessState;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\System\ProcessState
 */
class ProcessStateTest extends TestCase
{
    public function testClassInstantiation(): void {
        $state = new ProcessState();
        $this->assertInstanceOf(ProcessState::class, $state);
    }

    public function testWriteDataProperty(): void {
        $state = new ProcessState();
        $state->writeData = 'test data';
        $this->assertEquals('test data', $state->writeData);
    }

    public function testWriteDataPropertyDefault(): void {
        $state = new ProcessState();
        $this->assertNull($state->writeData);
    }

    public function testExitCodeProperty(): void {
        $state = new ProcessState();
        $state->exit_code = 0;
        $this->assertEquals(0, $state->exit_code);
    }

    public function testExitCodePropertyWithNonZero(): void {
        $state = new ProcessState();
        $state->exit_code = 1;
        $this->assertEquals(1, $state->exit_code);
    }

    public function testExitCodePropertyDefault(): void {
        $state = new ProcessState();
        $this->assertNull($state->exit_code);
    }

    public function testCwdProperty(): void {
        $state = new ProcessState();
        $state->setCwd('/tmp');
        $this->assertEquals('/tmp', $state->getCwd());
    }

    public function testCwdPropertyDefault(): void {
        $state = new ProcessState();
        $this->assertNull($state->getCwd());
    }

    public function testCwdWithNull(): void {
        $state = new ProcessState();
        $state->setCwd(null);
        $this->assertNull($state->getCwd());
    }

    public function testCwdWithEmptyString(): void {
        $state = new ProcessState();
        $state->setCwd('');
        $this->assertEquals('', $state->getCwd());
    }

    public function testEnvProperty(): void {
        $state = new ProcessState();
        $env = ['PATH' => '/usr/bin', 'HOME' => '/home/user'];
        $state->setEnv($env);
        $this->assertEquals($env, $state->getEnv());
    }

    public function testEnvPropertyDefault(): void {
        $state = new ProcessState();
        $this->assertIsArray($state->getEnv());
        $this->assertEmpty($state->getEnv());
    }

    public function testEnvWithEmptyArray(): void {
        $state = new ProcessState();
        $state->setEnv([]);
        $this->assertIsArray($state->getEnv());
        $this->assertEmpty($state->getEnv());
    }

    public function testNonBlockingModeProperty(): void {
        $state = new ProcessState();
        $state->nonBlockingMode = true;
        $this->assertTrue($state->nonBlockingMode);
    }

    public function testNonBlockingModePropertyDefault(): void {
        $state = new ProcessState();
        $this->assertFalse($state->nonBlockingMode);
    }

    public function testNonBlockTimeoutProperty(): void {
        $state = new ProcessState();
        $state->nonBlockTimeout = 1000000;
        $this->assertEquals(1000000, $state->nonBlockTimeout);
    }

    public function testNonBlockTimeoutPropertyDefault(): void {
        $state = new ProcessState();
        $this->assertEquals(500000, $state->nonBlockTimeout);
    }

    public function testRetValProperty(): void {
        $state = new ProcessState();
        $state->retVal = 'result';
        $this->assertEquals('result', $state->retVal);
    }

    public function testRetValPropertyDefault(): void {
        $state = new ProcessState();
        $this->assertNull($state->retVal);
    }

    public function testErrorProperty(): void {
        $state = new ProcessState();
        $state->error = 'Error message';
        $this->assertEquals('Error message', $state->error);
    }

    public function testErrorPropertyDefault(): void {
        $state = new ProcessState();
        $this->assertNull($state->error);
    }

    public function testCMDProperty(): void {
        $state = new ProcessState();
        $state->CMD = 'ls -la';
        $this->assertEquals('ls -la', $state->CMD);
    }

    public function testCMDPropertyDefault(): void {
        $state = new ProcessState();
        $this->assertNull($state->CMD);
    }

    public function testGetResult(): void {
        $state = new ProcessState();
        $state->retVal = 'test result';
        $this->assertEquals('test result', $state->getResult());
    }

    public function testGetResultWithNull(): void {
        $state = new ProcessState();
        $this->assertNull($state->getResult());
    }

    public function testGetError(): void {
        $state = new ProcessState();
        $state->error = 'Error occurred';
        $this->assertEquals('Error occurred', $state->getError());
    }

    public function testGetErrorWithWhitespace(): void {
        $state = new ProcessState();
        $state->error = "  Error with spaces  \n";
        $this->assertEquals('Error with spaces', $state->getError());
    }

    public function testGetErrorWithNull(): void {
        $state = new ProcessState();
        $this->assertEquals('', $state->getError());
    }

    public function testGetErrorWithEmptyString(): void {
        $state = new ProcessState();
        $state->error = '';
        $this->assertEquals('', $state->getError());
    }

    public function testDescribe(): void {
        $state = new ProcessState();
        $state->writeData = 'data';
        $state->retVal = 'result';
        $state->error = 'error';
        $state->exit_code = 0;
        $state->setCwd('/tmp');
        $state->setEnv(['PATH' => '/usr/bin']);
        $state->nonBlockingMode = true;
        $state->nonBlockTimeout = 1000;

        $result = $state->describe();
        $this->assertIsString($result);
    }

    public function testDescribeWithDefaultValues(): void {
        $state = new ProcessState();
        $result = $state->describe();
        $this->assertIsString($result);
    }

    public function testSetCwdReturnsVoid(): void {
        $state = new ProcessState();
        $result = $state->setCwd('/tmp');
        $this->assertNull($result);
    }

    public function testSetEnvReturnsVoid(): void {
        $state = new ProcessState();
        $result = $state->setEnv(['KEY' => 'value']);
        $this->assertNull($result);
    }

    public function testGetCwdReturnsMixed(): void {
        $state = new ProcessState();
        $result = $state->getCwd();
        $this->assertNull($result);
    }

    public function testGetEnvReturnsArray(): void {
        $state = new ProcessState();
        $result = $state->getEnv();
        $this->assertIsArray($result);
    }

    public function testGetResultReturnsMixed(): void {
        $state = new ProcessState();
        $result = $state->getResult();
        $this->assertNull($result);
    }

    public function testGetErrorReturnsString(): void {
        $state = new ProcessState();
        $result = $state->getError();
        $this->assertIsString($result);
    }

    public function testDescribeReturnsMixed(): void {
        $state = new ProcessState();
        $result = $state->describe();
        $this->assertIsString($result);
    }

    public function testAllPublicProperties(): void {
        $state = new ProcessState();

        $this->assertTrue(property_exists($state, 'writeData'));
        $this->assertTrue(property_exists($state, 'exit_code'));
        $this->assertTrue(property_exists($state, 'nonBlockingMode'));
        $this->assertTrue(property_exists($state, 'nonBlockTimeout'));
        $this->assertTrue(property_exists($state, 'retVal'));
        $this->assertTrue(property_exists($state, 'error'));
        $this->assertTrue(property_exists($state, 'CMD'));
    }

    public function testAllMethodsExist(): void {
        $state = new ProcessState();

        $this->assertTrue(method_exists($state, 'getCwd'));
        $this->assertTrue(method_exists($state, 'setCwd'));
        $this->assertTrue(method_exists($state, 'getEnv'));
        $this->assertTrue(method_exists($state, 'setEnv'));
        $this->assertTrue(method_exists($state, 'getResult'));
        $this->assertTrue(method_exists($state, 'getError'));
        $this->assertTrue(method_exists($state, 'describe'));
    }

    public function testMultipleStates(): void {
        $state1 = new ProcessState();
        $state2 = new ProcessState();

        $state1->exit_code = 0;
        $state2->exit_code = 1;

        $this->assertEquals(0, $state1->exit_code);
        $this->assertEquals(1, $state2->exit_code);
    }

    public function testStateModification(): void {
        $state = new ProcessState();

        $state->writeData = 'initial';
        $state->writeData = 'modified';

        $this->assertEquals('modified', $state->writeData);
    }

    public function testEnvModification(): void {
        $state = new ProcessState();

        $state->setEnv(['KEY1' => 'value1']);
        $state->setEnv(['KEY2' => 'value2']);

        $this->assertEquals(['KEY2' => 'value2'], $state->getEnv());
    }

    public function testCwdModification(): void {
        $state = new ProcessState();

        $state->setCwd('/tmp');
        $state->setCwd('/var');

        $this->assertEquals('/var', $state->getCwd());
    }

    public function testStateWithComplexData(): void {
        $state = new ProcessState();
        $state->writeData = ['complex' => ['nested' => 'data']];
        $this->assertIsArray($state->writeData);
    }

    public function testStateWithObject(): void {
        $state = new ProcessState();
        $obj = new \stdClass();
        $obj->property = 'value';
        $state->retVal = $obj;
        $this->assertInstanceOf(\stdClass::class, $state->retVal);
    }

    public function testStateClone(): void {
        $state1 = new ProcessState();
        $state1->exit_code = 0;
        $state1->CMD = 'test';

        $state2 = clone $state1;

        $this->assertEquals($state1->exit_code, $state2->exit_code);
        $this->assertEquals($state1->CMD, $state2->CMD);
    }

    public function testStateSerialization(): void {
        $state = new ProcessState();
        $state->exit_code = 0;
        $state->CMD = 'test';

        $serialized = serialize($state);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(ProcessState::class, $unserialized);
        $this->assertEquals(0, $unserialized->exit_code);
        $this->assertEquals('test', $unserialized->CMD);
    }
}
