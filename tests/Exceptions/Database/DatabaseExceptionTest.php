<?php
declare(strict_types=1);

/**
 * Test for Database exception classes.
 */

namespace Hubbitus\HuPHP\Tests\Exceptions\Database;

use Hubbitus\HuPHP\Exceptions\Database\DatabaseException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Database\DatabaseException
 */
class DatabaseExceptionTest extends TestCase {
    public function testDatabaseExceptionInstantiation(): void {
        $exception = new DatabaseException('Database error');
        $this->assertInstanceOf(DatabaseException::class, $exception);
    }

    public function testDatabaseExceptionExtendsBaseException(): void {
        $exception = new DatabaseException('Database error');
        $this->assertInstanceOf('Hubbitus\HuPHP\Exceptions\BaseException', $exception);
    }

    public function testDatabaseExceptionMessage(): void {
        $exception = new DatabaseException('Connection failed');
        $this->assertEquals('Connection failed', $exception->getMessage());
    }

    public function testDatabaseExceptionCode(): void {
        $exception = new DatabaseException('Database error', 500);
        $this->assertEquals(500, $exception->getCode());
    }

    public function testDatabaseExceptionCanBeThrown(): void {
        $this->expectException(DatabaseException::class);
        $this->expectExceptionMessage('Query failed');

        throw new DatabaseException('Query failed');
    }

    public function testDatabaseExceptionWithPrevious(): void {
        $previous = new \Exception('Previous error');
        $exception = new DatabaseException('Database error', 0, null, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testDatabaseExceptionInheritance(): void {
        $exception = new DatabaseException('Error');
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testDatabaseExceptionToString(): void {
        $exception = new DatabaseException('Database error');
        $string = (string) $exception;
        $this->assertIsString($string);
        $this->assertNotEmpty($string);
    }

    public function testDatabaseExceptionTrace(): void {
        $exception = new DatabaseException('Database error');
        $this->assertIsArray($exception->getTrace());
    }

    public function testDatabaseExceptionFile(): void {
        $exception = new DatabaseException('Database error');
        $this->assertIsString($exception->getFile());
    }

    public function testDatabaseExceptionLine(): void {
        $exception = new DatabaseException('Database error');
        $this->assertIsInt($exception->getLine());
    }

    public function testDatabaseExceptionSerialize(): void {
        $exception = new DatabaseException('Database error');
        $serialized = serialize($exception);
        $this->assertIsString($serialized);
    }

    public function testDatabaseExceptionJsonEncode(): void {
        $exception = new DatabaseException('Database error');
        $json = json_encode($exception);
        $this->assertIsString($json);
    }

    public function testDatabaseExceptionMultipleInstances(): void {
        $exception1 = new DatabaseException('Error 1');
        $exception2 = new DatabaseException('Error 2');

        $this->assertNotSame($exception1, $exception2);
        $this->assertNotEquals($exception1->getMessage(), $exception2->getMessage());
    }

    public function testDatabaseExceptionClone(): void {
        $exception1 = new DatabaseException('Database error');
        $exception2 = clone $exception1;

        $this->assertEquals($exception1->getMessage(), $exception2->getMessage());
        $this->assertEquals($exception1->getCode(), $exception2->getCode());
    }

    public function testDatabaseExceptionGetObjectId(): void {
        $exception = new DatabaseException('Database error');
        $id = spl_object_id($exception);
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testDatabaseExceptionGetObjectHash(): void {
        $exception = new DatabaseException('Database error');
        $hash = spl_object_hash($exception);
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function testDatabaseExceptionVarDump(): void {
        $exception = new DatabaseException('Database error');
        ob_start();
        var_dump($exception);
        $output = ob_get_clean();
        $this->assertIsString($output);
        $this->assertStringContainsString('DatabaseException', $output);
    }

    public function testDatabaseExceptionVarExport(): void {
        $exception = new DatabaseException('Database error');
        $export = var_export($exception, true);
        $this->assertIsString($export);
        $this->assertNotEmpty($export);
    }

    public function testDatabaseExceptionPrintR(): void {
        $exception = new DatabaseException('Database error');
        ob_start();
        print_r($exception);
        $output = ob_get_clean();
        $this->assertIsString($output);
        $this->assertStringContainsString('DatabaseException', $output);
    }

    public function testDatabaseExceptionTrueInBooleanContext(): void {
        $exception = new DatabaseException('Database error');
        $this->assertTrue((bool) $exception);
    }

    public function testDatabaseExceptionNotEmpty(): void {
        $exception = new DatabaseException('Database error');
        $this->assertFalse(empty($exception));
    }

    public function testDatabaseExceptionIsset(): void {
        $exception = new DatabaseException('Database error');
        $this->assertTrue(isset($exception));
    }

    public function testDatabaseExceptionGetType(): void {
        $exception = new DatabaseException('Database error');
        $this->assertEquals('object', gettype($exception));
    }

    public function testDatabaseExceptionGetClassName(): void {
        $exception = new DatabaseException('Database error');
        $this->assertEquals('Hubbitus\HuPHP\Exceptions\Database\DatabaseException', get_class($exception));
    }

    public function testDatabaseExceptionInArray(): void {
        $exception = new DatabaseException('Database error');
        $array = [$exception];
        $this->assertContains($exception, $array);
    }

    public function testDatabaseExceptionAsArrayValue(): void {
        $exception = new DatabaseException('Database error');
        $array = ['exception' => $exception];
        $this->assertInstanceOf(DatabaseException::class, $array['exception']);
    }

    public function testDatabaseExceptionUniqueObjectIds(): void {
        $exception1 = new DatabaseException('Error 1');
        $exception2 = new DatabaseException('Error 2');

        $id1 = spl_object_id($exception1);
        $id2 = spl_object_id($exception2);

        $this->assertNotEquals($id1, $id2);
    }

    public function testDatabaseExceptionChain(): void {
        $exception1 = new DatabaseException('First error');
        $exception2 = new DatabaseException('Second error', 0, null, $exception1);

        $this->assertSame($exception1, $exception2->getPrevious());
    }

    public function testDatabaseExceptionNesting(): void {
        try {
            try {
                throw new DatabaseException('Inner error');
            } catch (DatabaseException $e) {
                throw new DatabaseException('Outer error', 0, null, $e);
            }
        } catch (DatabaseException $e) {
            $this->assertInstanceOf(DatabaseException::class, $e);
            $this->assertInstanceOf(DatabaseException::class, $e->getPrevious());
        }
    }

    public function testDatabaseExceptionWithErrorCode(): void {
        $exception = new DatabaseException('Connection refused', 503);
        $this->assertEquals(503, $exception->getCode());
        $this->assertEquals('Connection refused', $exception->getMessage());
    }

    public function testDatabaseExceptionWithZeroCode(): void {
        $exception = new DatabaseException('Database error', 0);
        $this->assertEquals(0, $exception->getCode());
    }

    public function testDatabaseExceptionInheritanceChain(): void {
        $exception = new DatabaseException('Error');

        $this->assertInstanceOf(DatabaseException::class, $exception);
        $this->assertInstanceOf('Hubbitus\HuPHP\Exceptions\BaseException', $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }
}
