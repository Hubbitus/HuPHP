<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Exceptions;

use Hubbitus\HuPHP\Exceptions\BaseException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassMethodException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassNotExistsException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use Hubbitus\HuPHP\Exceptions\Classes\ClassUnknownException;
use Hubbitus\HuPHP\Exceptions\Filesystem\FileException;
use Hubbitus\HuPHP\Exceptions\NotImplementedException;
use Hubbitus\HuPHP\Exceptions\ProcessException;
use Hubbitus\HuPHP\Exceptions\SerializeException;
use Hubbitus\HuPHP\Exceptions\SessionException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableReadOnlyException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Exceptions\BaseException
* @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableException
* @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableEmptyException
* @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException
* @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableReadOnlyException
* @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException
* @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassException
* @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassMethodException
* @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassNotExistsException
* @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException
* @covers \Hubbitus\HuPHP\Exceptions\Classes\ClassUnknownException
* @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileException
* @covers \Hubbitus\HuPHP\Exceptions\ProcessException
* @covers \Hubbitus\HuPHP\Exceptions\SerializeException
* @covers \Hubbitus\HuPHP\Exceptions\SessionException
* @covers \Hubbitus\HuPHP\Exceptions\NotImplementedException
**/
class ExceptionHierarchyTest extends TestCase {
	public function testBaseExceptionExtendsException(): void {
		$exception = new BaseException('Test message');

		$this->assertInstanceOf(\Exception::class, $exception);
		$this->assertInstanceOf(BaseException::class, $exception);
	}

	public function testBaseExceptionWithMessage(): void {
		$exception = new BaseException('Test message');

		$this->assertEquals('Test message', $exception->getMessage());
	}

	public function testBaseExceptionWithCode(): void {
		$exception = new BaseException('Test message', 42);

		$this->assertEquals(42, $exception->getCode());
	}

	public function testBaseExceptionWithPrevious(): void {
		$previous = new \Exception('Previous');
		$exception = new BaseException('Test message', 0, $previous);

		$this->assertSame($previous, $exception->getPrevious());
	}

	public function testBaseExceptionADDMessageAppend(): void {
		$exception = new BaseException('Original');
		$exception->ADDMessage(' Added');

		$this->assertEquals('Original Added', $exception->getMessage());
	}

	public function testBaseExceptionADDMessagePrepend(): void {
		$exception = new BaseException('Original');
		$exception->ADDMessage('Added ', true);

		$this->assertEquals('Added Original', $exception->getMessage());
	}

	public function testVariableExceptionExtendsBaseException(): void {
		$exception = new VariableException('Test message');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(VariableException::class, $exception);
	}

	public function testVariableEmptyExceptionExtendsVariableException(): void {
		// VariableEmptyException extends VariableRequiredException which requires Backtrace
		// We test the class hierarchy by checking reflection
		$reflection = new \ReflectionClass(VariableEmptyException::class);

		$this->assertTrue($reflection->isSubclassOf(VariableException::class));
		$this->assertTrue($reflection->isSubclassOf(BaseException::class));
	}

	public function testVariableRangeException(): void {
		$exception = new VariableRangeException('Test message');

		$this->assertInstanceOf(VariableException::class, $exception);
		$this->assertInstanceOf(VariableRangeException::class, $exception);
	}

	public function testVariableReadOnlyException(): void {
		$exception = new VariableReadOnlyException('Test message');

		$this->assertInstanceOf(VariableException::class, $exception);
		$this->assertInstanceOf(VariableReadOnlyException::class, $exception);
	}

	public function testVariableRequiredException(): void {
		// VariableRequiredException requires Backtrace object
		// We test the class hierarchy by checking reflection
		$reflection = new \ReflectionClass(VariableRequiredException::class);

		$this->assertTrue($reflection->isSubclassOf(VariableException::class));
		$this->assertTrue($reflection->isSubclassOf(BaseException::class));
	}

	public function testClassExceptionExtendsBaseException(): void {
		$exception = new ClassException('Test message');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(ClassException::class, $exception);
	}

	public function testClassMethodException(): void {
		$exception = new ClassMethodException('Test message');

		$this->assertInstanceOf(ClassException::class, $exception);
		$this->assertInstanceOf(ClassMethodException::class, $exception);
	}

	public function testClassNotExistsException(): void {
		$exception = new ClassNotExistsException('Test message');

		$this->assertInstanceOf(ClassException::class, $exception);
		$this->assertInstanceOf(ClassNotExistsException::class, $exception);
	}

	public function testClassPropertyNotExistsException(): void {
		$exception = new ClassPropertyNotExistsException('Test message');

		$this->assertInstanceOf(ClassException::class, $exception);
		$this->assertInstanceOf(ClassPropertyNotExistsException::class, $exception);
	}

	public function testClassUnknownException(): void {
		$exception = new ClassUnknownException('Test message');

		$this->assertInstanceOf(ClassException::class, $exception);
		$this->assertInstanceOf(ClassUnknownException::class, $exception);
	}

	public function testFileException(): void {
		// FileException requires fullPath parameter
		$exception = new FileException('Test message', '/path/to/file');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(FileException::class, $exception);
		$this->assertEquals('/path/to/file', $exception->getFullPath());
	}

	public function testProcessException(): void {
		// ProcessException requires state parameter
		$exception = new ProcessException('Test message', 0, 'running');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(ProcessException::class, $exception);
		$this->assertEquals('running', $exception->state);
	}

	public function testSerializeException(): void {
		$exception = new SerializeException('Test message');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(SerializeException::class, $exception);
	}

	public function testSessionException(): void {
		$exception = new SessionException('Test message');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(SessionException::class, $exception);
	}

	public function testNotImplementedException(): void {
		$exception = new NotImplementedException('Test message');

		$this->assertInstanceOf(BaseException::class, $exception);
		$this->assertInstanceOf(NotImplementedException::class, $exception);
	}
}
