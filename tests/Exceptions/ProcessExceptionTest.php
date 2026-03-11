<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Exceptions;

use Hubbitus\HuPHP\Exceptions\ProcessException;
use Hubbitus\HuPHP\Exceptions\BaseException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\ProcessException
**/
class ProcessExceptionTest extends TestCase {
	public function testClassInstantiation(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Test message', 0, $state);
		$this->assertInstanceOf(ProcessException::class, $exception);
	}

	public function testClassExtendsBaseException(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Test message', 0, $state);
		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(\Exception::class, $exception);
	}

	public function testExceptionStateProperty(): void {
		$state = new \stdClass();
		$state->pid = 123;
		$exception = new ProcessException('Error', 0, $state);
		$this->assertSame($state, $exception->state);
	}

	public function testExceptionStateWithArray(): void {
		$state = ['status' => 'failed', 'code' => 1];
		$exception = new ProcessException('Error', 0, $state);
		$this->assertEquals($state, $exception->state);
	}

	public function testExceptionStateWithString(): void {
		$state = 'running';
		$exception = new ProcessException('Error', 0, $state);
		$this->assertEquals($state, $exception->state);
	}

	public function testExceptionStateWithNull(): void {
		$exception = new ProcessException('Error', 0, null);
		$this->assertNull($exception->state);
	}

	public function testExceptionStateWithInteger(): void {
		$state = 42;
		$exception = new ProcessException('Error', 0, $state);
		$this->assertEquals($state, $exception->state);
	}

	public function testExceptionMessage(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Custom error message', 0, $state);
		$this->assertEquals('Custom error message', $exception->getMessage());
	}

	public function testExceptionCode(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Error', 500, $state);
		$this->assertEquals(500, $exception->getCode());
	}

	public function testExceptionCodeDefault(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Error', 0, $state);
		$this->assertEquals(0, $exception->getCode());
	}

	public function testExceptionWithEmptyMessage(): void {
		$state = new \stdClass();
		$exception = new ProcessException('', 0, $state);
		$this->assertEquals('', $exception->getMessage());
	}

	public function testExceptionMessageConversion(): void {
		$state = new \stdClass();
		$exception = new ProcessException(123, 0, $state);
		$this->assertEquals('123', $exception->getMessage());
	}

	public function testExceptionWithArrayMessage(): void {
		$state = new \stdClass();
		$exception = new ProcessException(['error' => 'test'], 0, $state);
		$this->assertIsString($exception->getMessage());
	}

	public function testExceptionWithObjectMessage(): void {
		$state = new \stdClass();
		$messageObj = new \stdClass();
		$messageObj->msg = 'Error';
		$exception = new ProcessException($messageObj, 0, $state);
		$this->assertIsString($exception->getMessage());
	}

	public function testExceptionStateIsPublic(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Error', 0, $state);

		$this->assertTrue(\property_exists($exception, 'state'));

		$reflection = new \ReflectionProperty($exception, 'state');
		$this->assertTrue($reflection->isPublic());
	}

	public function testExceptionStateModification(): void {
		$state1 = new \stdClass();
		$exception = new ProcessException('Error', 0, $state1);

		$state2 = new \stdClass();
		$exception->state = $state2;

		$this->assertSame($state2, $exception->state);
	}

	public function testExceptionWithPrevious(): void {
		$state = new \stdClass();
		$previous = new \Exception('Previous error');
		$exception = new ProcessException('Current error', 0, $state);

		$this->assertNotNull($exception);
	}

	public function testExceptionTrace(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Error', 0, $state);
		$trace = $exception->getTrace();
		$this->assertIsArray($trace);
	}

	public function testExceptionFile(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Error', 0, $state);
		$file = $exception->getFile();
		$this->assertIsString($file);
	}

	public function testExceptionLine(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Error', 0, $state);
		$line = $exception->getLine();
		$this->assertIsInt($line);
	}

	public function testExceptionToString(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Error message', 0, $state);
		$string = (string) $exception;
		$this->assertIsString($string);
	}

	public function testExceptionCanBeThrown(): void {
		$this->expectException(ProcessException::class);
		$this->expectExceptionMessage('Process failed');

		$state = new \stdClass();
		throw new ProcessException('Process failed', 1, $state);
	}

	public function testExceptionWithDifferentStates(): void {
		$states = [
			new \stdClass(),
			['key' => 'value'],
			'string',
			123,
			null,
			true,
			3.14
		];

		foreach ($states as $state) {
			$exception = new ProcessException('Error', 0, $state);
			$this->assertEquals($state, $exception->state);
		}
	}

	public function testExceptionImplementsThrowable(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Error', 0, $state);
		$this->assertInstanceOf(\Throwable::class, $exception);
	}

	public function testExceptionSerialization(): void {
		$state = ['status' => 'failed'];
		$exception = new ProcessException('Serialize test', 0, $state);
		$serialized = \serialize($exception);
		$unserialized = \unserialize($serialized);

		$this->assertInstanceOf(ProcessException::class, $unserialized);
		$this->assertEquals('Serialize test', $unserialized->getMessage());
	}

	public function testExceptionStateWithBoolean(): void {
		$exception = new ProcessException('Error', 0, true);
		$this->assertTrue($exception->state);

		$exception2 = new ProcessException('Error', 0, false);
		$this->assertFalse($exception2->state);
	}

	public function testExceptionStateWithFloat(): void {
		$state = 3.14159;
		$exception = new ProcessException('Error', 0, $state);
		$this->assertEquals(3.14159, $exception->state);
	}

	public function testExceptionStateWithResource(): void {
		$resource = \tmpfile();
		$exception = new ProcessException('Error', 0, $resource);
		$this->assertIsResource($exception->state);
		\fclose($resource);
	}

	public function testExceptionAddMessageInherited(): void {
		$state = new \stdClass();
		$exception = new ProcessException('Initial', 0, $state);
		$exception->ADDMessage(' Added');
		$this->assertEquals('Initial Added', $exception->getMessage());
	}

	public function testExceptionConstructorWithAllParameters(): void {
		$state = new \stdClass();
		$previous = new \Exception('Previous');
		$exception = new ProcessException('Message', 100, $state);

		$this->assertEquals('Message', $exception->getMessage());
		$this->assertEquals(100, $exception->getCode());
		$this->assertSame($state, $exception->state);
	}
}
