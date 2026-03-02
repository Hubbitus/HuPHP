<?php
declare(strict_types=1);

/**
 * Test for simple Variable exception classes.
 */

namespace Hubbitus\HuPHP\Tests\Exceptions\Variables;

use Hubbitus\HuPHP\Exceptions\Variables\VariableException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableReadOnlyException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableArrayInconsistentException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableIsNullException;
use Hubbitus\HuPHP\Exceptions\BaseException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Variables\VariableException
 */
class VariableExceptionsTest extends TestCase {
    public function testVariableExceptionInstantiation(): void {
        $exception = new VariableException('Variable error');
        $this->assertInstanceOf(VariableException::class, $exception);
    }

    public function testVariableExceptionExtendsBaseException(): void {
        $exception = new VariableException('Variable error');
        $this->assertInstanceOf(BaseException::class, $exception);
    }

    public function testVariableExceptionMessage(): void {
        $exception = new VariableException('Custom variable error');
        $this->assertEquals('Custom variable error', $exception->getMessage());
    }

    public function testVariableExceptionCode(): void {
        $exception = new VariableException('Variable error', 400);
        $this->assertEquals(400, $exception->getCode());
    }

    public function testVariableExceptionCanBeThrown(): void {
        $this->expectException(VariableException::class);
        $this->expectExceptionMessage('Variable error occurred');

        throw new VariableException('Variable error occurred');
    }

    public function testVariableExceptionWithPrevious(): void {
        $previous = new \Exception('Previous error');
        $exception = new VariableException('Variable error', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testVariableRangeExceptionInstantiation(): void {
        $exception = new VariableRangeException('Value out of range');
        $this->assertInstanceOf(VariableRangeException::class, $exception);
    }

    public function testVariableRangeExceptionExtendsVariableException(): void {
        $exception = new VariableRangeException('Value out of range');
        $this->assertInstanceOf(VariableException::class, $exception);
    }

    public function testVariableRangeExceptionMessage(): void {
        $exception = new VariableRangeException('Value must be between 1 and 10');
        $this->assertEquals('Value must be between 1 and 10', $exception->getMessage());
    }

    public function testVariableRangeExceptionCode(): void {
        $exception = new VariableRangeException('Out of range', 422);
        $this->assertEquals(422, $exception->getCode());
    }

    public function testVariableRangeExceptionCanBeThrown(): void {
        $this->expectException(VariableRangeException::class);
        $this->expectExceptionMessage('Value out of valid range');

        throw new VariableRangeException('Value out of valid range');
    }

    public function testVariableRangeExceptionInheritance(): void {
        $exception = new VariableRangeException('Error');
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(BaseException::class, $exception);
    }

    public function testVariableReadOnlyExceptionInstantiation(): void {
        $exception = new VariableReadOnlyException('Cannot modify read-only variable');
        $this->assertInstanceOf(VariableReadOnlyException::class, $exception);
    }

    public function testVariableReadOnlyExceptionExtendsVariableException(): void {
        $exception = new VariableReadOnlyException('Cannot modify read-only variable');
        $this->assertInstanceOf(VariableException::class, $exception);
    }

    public function testVariableReadOnlyExceptionMessage(): void {
        $exception = new VariableReadOnlyException('Property is read-only');
        $this->assertEquals('Property is read-only', $exception->getMessage());
    }

    public function testVariableReadOnlyExceptionCode(): void {
        $exception = new VariableReadOnlyException('Read-only', 403);
        $this->assertEquals(403, $exception->getCode());
    }

    public function testVariableReadOnlyExceptionCanBeThrown(): void {
        $this->expectException(VariableReadOnlyException::class);
        $this->expectExceptionMessage('Cannot assign to read-only property');

        throw new VariableReadOnlyException('Cannot assign to read-only property');
    }

    public function testVariableReadOnlyExceptionInheritance(): void {
        $exception = new VariableReadOnlyException('Error');
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(BaseException::class, $exception);
    }

    public function testVariableArrayInconsistentExceptionInstantiation(): void {
        $exception = new VariableArrayInconsistentException('Array is inconsistent');
        $this->assertInstanceOf(VariableArrayInconsistentException::class, $exception);
    }

    public function testVariableArrayInconsistentExceptionMessage(): void {
        $exception = new VariableArrayInconsistentException('Array keys do not match');
        $this->assertEquals('Array keys do not match', $exception->getMessage());
    }

    public function testVariableArrayInconsistentExceptionCode(): void {
        $exception = new VariableArrayInconsistentException('Inconsistent', 422);
        $this->assertEquals(422, $exception->getCode());
    }

    public function testVariableArrayInconsistentExceptionCanBeThrown(): void {
        $this->expectException(VariableArrayInconsistentException::class);
        $this->expectExceptionMessage('Array structure is inconsistent');

        throw new VariableArrayInconsistentException('Array structure is inconsistent');
    }

    public function testVariableArrayInconsistentExceptionInheritance(): void {
        $exception = new VariableArrayInconsistentException('Error');
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testVariableIsNullExceptionInstantiation(): void {
        $exception = new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Variable is null');
        $this->assertInstanceOf(VariableIsNullException::class, $exception);
    }

    public function testVariableIsNullExceptionMessage(): void {
        $exception = new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), null, 'Required variable is null');
        $this->assertEquals('Required variable is null', $exception->getMessage());
    }

    public function testVariableIsNullExceptionCode(): void {
        $exception = new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Null value', null, 400);
        $this->assertEquals(400, $exception->getCode());
    }

    public function testVariableIsNullExceptionCanBeThrown(): void {
        $this->expectException(VariableIsNullException::class);
        $this->expectExceptionMessage('Variable cannot be null');

        throw new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), null, 'Variable cannot be null');
    }

    public function testVariableIsNullExceptionInheritance(): void {
        $exception = new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Error');
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testAllExceptionsAreThrowable(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $this->assertInstanceOf(\Throwable::class, $exception);
        }
    }

    public function testAllExceptionsExtendException(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $this->assertInstanceOf(\Exception::class, $exception);
        }
    }

    public function testAllExceptionsHaveMessage(): void {
        $exceptions = [
            new VariableException('Test message 1'),
            new VariableRangeException('Test message 2'),
            new VariableReadOnlyException('Test message 3'),
            new VariableArrayInconsistentException('Test message 4'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), null, 'Test message 5')
        ];

        foreach ($exceptions as $index => $exception) {
            $this->assertStringContainsString('Test message ' . ($index + 1), $exception->getMessage());
        }
    }

    public function testAllExceptionsHaveCode(): void {
        $exceptions = [
            new VariableException('Test', 1),
            new VariableRangeException('Test', 2),
            new VariableReadOnlyException('Test', 3),
            new VariableArrayInconsistentException('Test', 4),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test', null, 5)
        ];

        $codes = [1, 2, 3, 4, 5];
        foreach ($exceptions as $index => $exception) {
            $this->assertEquals($codes[$index], $exception->getCode());
        }
    }

    public function testAllExceptionsHaveFile(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $this->assertIsString($exception->getFile());
            $this->assertNotEmpty($exception->getFile());
        }
    }

    public function testAllExceptionsHaveLine(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $this->assertIsInt($exception->getLine());
            $this->assertGreaterThan(0, $exception->getLine());
        }
    }

    public function testAllExceptionsHaveTrace(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $this->assertIsArray($exception->getTrace());
        }
    }

    public function testAllExceptionsHaveTraceAsString(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $this->assertIsString($exception->getTraceAsString());
        }
    }

    public function testAllExceptionsCanBeSerialized(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $serialized = serialize($exception);
            $this->assertIsString($serialized);
        }
    }

    public function testAllExceptionsCanBeJsonEncoded(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $json = json_encode($exception);
            $this->assertIsString($json);
        }
    }

    public function testAllExceptionsAreObjects(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $this->assertIsObject($exception);
        }
    }

    public function testAllExceptionsAreNotNull(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $this->assertNotNull($exception);
        }
    }

    public function testAllExceptionsGetType(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $this->assertEquals('object', gettype($exception));
        }
    }

    public function testAllExceptionsGetClassName(): void {
        $exception1 = new VariableException('Test');
        $this->assertEquals('Hubbitus\HuPHP\Exceptions\Variables\VariableException', get_class($exception1));

        $exception2 = new VariableRangeException('Test');
        $this->assertEquals('Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException', get_class($exception2));

        $exception3 = new VariableReadOnlyException('Test');
        $this->assertEquals('Hubbitus\HuPHP\Exceptions\Variables\VariableReadOnlyException', get_class($exception3));

        $exception4 = new VariableArrayInconsistentException('Test');
        $this->assertEquals('Hubbitus\HuPHP\Exceptions\Variables\VariableArrayInconsistentException', get_class($exception4));

        $exception5 = new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test');
        $this->assertEquals('Hubbitus\HuPHP\Exceptions\Variables\VariableIsNullException', get_class($exception5));
    }

    public function testExceptionInheritanceChain(): void {
        $exception = new VariableRangeException('Error');

        $this->assertInstanceOf(VariableRangeException::class, $exception);
        $this->assertInstanceOf(VariableException::class, $exception);
        $this->assertInstanceOf(BaseException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testReadOnlyExceptionInheritanceChain(): void {
        $exception = new VariableReadOnlyException('Error');

        $this->assertInstanceOf(VariableReadOnlyException::class, $exception);
        $this->assertInstanceOf(VariableException::class, $exception);
        $this->assertInstanceOf(BaseException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testArrayInconsistentExceptionInheritanceChain(): void {
        $exception = new VariableArrayInconsistentException('Error');

        $this->assertInstanceOf(VariableArrayInconsistentException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testNullExceptionInheritanceChain(): void {
        $exception = new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Error');

        $this->assertInstanceOf(VariableIsNullException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testMultipleInstancesOfSameException(): void {
        $exception1 = new VariableException('Test 1');
        $exception2 = new VariableException('Test 2');

        $this->assertNotSame($exception1, $exception2);
        $this->assertNotEquals($exception1->getMessage(), $exception2->getMessage());
    }

    public function testExceptionsWithSameMessageAreEqual(): void {
        $exception1 = new VariableException('Same message');
        $exception2 = new VariableException('Same message');

        $this->assertEquals($exception1->getMessage(), $exception2->getMessage());
        $this->assertEquals($exception1, $exception2);
    }

    public function testExceptionChain(): void {
        $exception1 = new VariableException('First');
        $exception2 = new VariableRangeException('Second', 0, $exception1);
        $exception3 = new VariableReadOnlyException('Third', 0, $exception2);

        $this->assertSame($exception2, $exception3->getPrevious());
        $this->assertSame($exception1, $exception2->getPrevious());
    }

    public function testExceptionNesting(): void {
        try {
            try {
                throw new VariableException('Inner');
            } catch (VariableException $e) {
                throw new VariableRangeException('Outer', 0, $e);
            }
        } catch (VariableRangeException $e) {
            $this->assertInstanceOf(VariableRangeException::class, $e);
            $this->assertInstanceOf(VariableException::class, $e->getPrevious());
        }
    }

    public function testExceptionToString(): void {
        $exceptions = [
            new VariableException('Test'),
            new VariableRangeException('Test'),
            new VariableReadOnlyException('Test'),
            new VariableArrayInconsistentException('Test'),
            new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), 'Test')
        ];

        foreach ($exceptions as $exception) {
            $string = (string) $exception;
            $this->assertIsString($string);
            $this->assertNotEmpty($string);
        }
    }

    public function testExceptionVarDump(): void {
        $exception = new VariableException('Test');
        ob_start();
        var_dump($exception);
        $output = ob_get_clean();
        $this->assertIsString($output);
        $this->assertStringContainsString('VariableException', $output);
    }

    public function testExceptionVarExport(): void {
        $exception = new VariableException('Test');
        $export = var_export($exception, true);
        $this->assertIsString($export);
        $this->assertNotEmpty($export);
    }

    public function testExceptionPrintR(): void {
        $exception = new VariableException('Test');
        ob_start();
        print_r($exception);
        $output = ob_get_clean();
        $this->assertIsString($output);
        $this->assertStringContainsString('VariableException', $output);
    }


    public function testExceptionNotSame(): void {
        $exception1 = new VariableException('Test');
        $exception2 = new VariableException('Test');

        $this->assertEquals($exception1, $exception2);
        $this->assertNotSame($exception1, $exception2);
    }

    public function testExceptionInArray(): void {
        $exception = new VariableException('Test');
        $array = [$exception];
        $this->assertContains($exception, $array);
    }

    public function testExceptionAsArrayValue(): void {
        $exception = new VariableException('Test');
        $array = ['exception' => $exception];
        $this->assertInstanceOf(VariableException::class, $array['exception']);
    }

    public function testExceptionGetObjectId(): void {
        $exception = new VariableException('Test');
        $id = spl_object_id($exception);
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testExceptionGetObjectHash(): void {
        $exception = new VariableException('Test');
        $hash = spl_object_hash($exception);
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function testExceptionUniqueObjectIds(): void {
        $exception1 = new VariableException('Test 1');
        $exception2 = new VariableException('Test 2');

        $id1 = spl_object_id($exception1);
        $id2 = spl_object_id($exception2);

        $this->assertNotEquals($id1, $id2);
    }

    public function testExceptionTrueInBooleanContext(): void {
        $exception = new VariableException('Test');
        $this->assertTrue((bool) $exception);
    }

    public function testExceptionNotEmpty(): void {
        $exception = new VariableException('Test');
        $this->assertFalse(empty($exception));
    }

    public function testExceptionIsset(): void {
        $exception = new VariableException('Test');
        $this->assertTrue(isset($exception));
    }

    public function testRangeExceptionSpecificMessage(): void {
        $exception = new VariableRangeException('Value 15 is out of range [1-10]');
        $this->assertStringContainsString('out of range', strtolower($exception->getMessage()));
    }

    public function testReadOnlyExceptionSpecificMessage(): void {
        $exception = new VariableReadOnlyException('Cannot modify read-only property $name');
        $this->assertStringContainsString('read-only', strtolower($exception->getMessage()));
    }

    public function testArrayInconsistentExceptionSpecificMessage(): void {
        $exception = new VariableArrayInconsistentException('Array has inconsistent keys');
        $this->assertStringContainsString('inconsistent', strtolower($exception->getMessage()));
    }

    public function testNullExceptionSpecificMessage(): void {
        $exception = new VariableIsNullException(new \Hubbitus\HuPHP\Debug\Backtrace(), null, 'Required parameter $id is null');
        $this->assertStringContainsString('null', strtolower($exception->getMessage()));
    }
}
