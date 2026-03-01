<?php
declare(strict_types=1);

/**
 * Test for Filesystem exception classes.
 */

namespace Hubbitus\HuPHP\Tests\Exceptions\Filesystem;

use Hubbitus\HuPHP\Exceptions\Filesystem\FileException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Exceptions\Filesystem\FileException
 */
class FileExceptionTest extends TestCase {
    public function testFileExceptionInstantiation(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertInstanceOf(FileException::class, $exception);
    }

    public function testFileExceptionExtendsBaseException(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertInstanceOf('Hubbitus\HuPHP\Exceptions\BaseException', $exception);
    }

    public function testFileExceptionMessage(): void {
        $exception = new FileException('File not found', '/path/to/file');
        $this->assertEquals('File not found', $exception->getMessage());
    }

    public function testFileExceptionFilename(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertEquals('/path/to/file', $exception->filename);
    }

    public function testFileExceptionCode(): void {
        $exception = new FileException('File error', '/path/to/file', 404);
        $this->assertEquals(404, $exception->getCode());
    }

    public function testFileExceptionCanBeThrown(): void {
        $this->expectException(FileException::class);
        $this->expectExceptionMessage('Cannot read file');

        throw new FileException('Cannot read file', '/path/to/file');
    }

    public function testFileExceptionWithPrevious(): void {
        $previous = new \Exception('Previous error');
        $exception = new FileException('File error', '/path/to/file', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testFileExceptionInheritance(): void {
        $exception = new FileException('Error', '/path/to/file');
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testFileExceptionToString(): void {
        $exception = new FileException('File error', '/path/to/file');
        $string = (string) $exception;
        $this->assertIsString($string);
        $this->assertNotEmpty($string);
    }

    public function testFileExceptionTrace(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertIsArray($exception->getTrace());
    }

    public function testFileExceptionFile(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertIsString($exception->getFile());
    }

    public function testFileExceptionLine(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertIsInt($exception->getLine());
    }

    public function testFileExceptionSerialize(): void {
        $exception = new FileException('File error', '/path/to/file');
        $serialized = serialize($exception);
        $this->assertIsString($serialized);
    }

    public function testFileExceptionJsonEncode(): void {
        $exception = new FileException('File error', '/path/to/file');
        $json = json_encode($exception);
        $this->assertIsString($json);
    }

    public function testFileExceptionWithEmptyFilename(): void {
        $exception = new FileException('File error', '');
        $this->assertEquals('', $exception->filename);
    }

    public function testFileExceptionWithEmptyStringFilename(): void {
        $exception = new FileException('File error', '');
        $this->assertIsString($exception->filename);
        $this->assertEmpty($exception->filename);
    }

    public function testFileExceptionPublicFilenameProperty(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertTrue(property_exists($exception, 'filename'));

        $reflection = new \ReflectionProperty($exception, 'filename');
        $this->assertTrue($reflection->isPublic());
    }

    public function testFileExceptionFilenameModification(): void {
        $exception = new FileException('File error', '/path/to/file');
        $exception->filename = '/new/path/to/file';
        $this->assertEquals('/new/path/to/file', $exception->filename);
    }

    public function testFileExceptionMultipleInstances(): void {
        $exception1 = new FileException('Error 1', '/file1.txt');
        $exception2 = new FileException('Error 2', '/file2.txt');

        $this->assertNotSame($exception1, $exception2);
        $this->assertNotEquals($exception1->filename, $exception2->filename);
    }

    public function testFileExceptionCloneIsNotSupported(): void {
        // Note: Exception objects cannot be cloned in PHP
        $exception = new FileException('File error', '/path/to/file');
        
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Trying to clone an uncloneable object');
        
        clone $exception;
    }

    public function testFileExceptionGetObjectId(): void {
        $exception = new FileException('File error', '/path/to/file');
        $id = spl_object_id($exception);
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testFileExceptionGetObjectHash(): void {
        $exception = new FileException('File error', '/path/to/file');
        $hash = spl_object_hash($exception);
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function testFileExceptionVarDump(): void {
        $exception = new FileException('File error', '/path/to/file');
        ob_start();
        var_dump($exception);
        $output = ob_get_clean();
        $this->assertIsString($output);
        $this->assertStringContainsString('FileException', $output);
    }

    public function testFileExceptionVarExport(): void {
        $exception = new FileException('File error', '/path/to/file');
        $export = var_export($exception, true);
        $this->assertIsString($export);
        $this->assertNotEmpty($export);
    }

    public function testFileExceptionPrintR(): void {
        $exception = new FileException('File error', '/path/to/file');
        ob_start();
        print_r($exception);
        $output = ob_get_clean();
        $this->assertIsString($output);
        $this->assertStringContainsString('FileException', $output);
    }

    public function testFileExceptionTrueInBooleanContext(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertTrue((bool) $exception);
    }

    public function testFileExceptionNotEmpty(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertFalse(empty($exception));
    }

    public function testFileExceptionIsset(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertTrue(isset($exception));
    }

    public function testFileExceptionGetType(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertEquals('object', gettype($exception));
    }

    public function testFileExceptionGetClassName(): void {
        $exception = new FileException('File error', '/path/to/file');
        $this->assertEquals('Hubbitus\HuPHP\Exceptions\Filesystem\FileException', get_class($exception));
    }

    public function testFileExceptionInArray(): void {
        $exception = new FileException('File error', '/path/to/file');
        $array = [$exception];
        $this->assertContains($exception, $array);
    }

    public function testFileExceptionAsArrayValue(): void {
        $exception = new FileException('File error', '/path/to/file');
        $array = ['exception' => $exception];
        $this->assertInstanceOf(FileException::class, $array['exception']);
    }

    public function testFileExceptionUniqueObjectIds(): void {
        $exception1 = new FileException('Error 1', '/file1.txt');
        $exception2 = new FileException('Error 2', '/file2.txt');

        $id1 = spl_object_id($exception1);
        $id2 = spl_object_id($exception2);

        $this->assertNotEquals($id1, $id2);
    }

    public function testFileExceptionWithSpecialCharactersInFilename(): void {
        $exception = new FileException('File error', '/path/with spaces/file&name.txt');
        $this->assertEquals('/path/with spaces/file&name.txt', $exception->filename);
    }

    public function testFileExceptionWithUnicodeFilename(): void {
        $exception = new FileException('File error', '/путь/к/файлу.txt');
        $this->assertEquals('/путь/к/файлу.txt', $exception->filename);
    }

    public function testFileExceptionWithLongFilename(): void {
        $longFilename = str_repeat('/path', 100) . '/file.txt';
        $exception = new FileException('File error', $longFilename);
        $this->assertEquals($longFilename, $exception->filename);
    }

    public function testFileExceptionChain(): void {
        $exception1 = new FileException('First error', '/file1.txt');
        $exception2 = new FileException('Second error', '/file2.txt', 0, $exception1);

        $this->assertSame($exception1, $exception2->getPrevious());
    }

    public function testFileExceptionNesting(): void {
        try {
            try {
                throw new FileException('Inner error', '/inner/file.txt');
            } catch (FileException $e) {
                throw new FileException('Outer error', '/outer/file.txt', 0, $e);
            }
        } catch (FileException $e) {
            $this->assertInstanceOf(FileException::class, $e);
            $this->assertInstanceOf(FileException::class, $e->getPrevious());
        }
    }

    public function testFileExceptionWithErrorCode(): void {
        $exception = new FileException('Permission denied', '/path/to/file', 403);
        $this->assertEquals(403, $exception->getCode());
        $this->assertEquals('Permission denied', $exception->getMessage());
    }

    public function testFileExceptionWithZeroCode(): void {
        $exception = new FileException('File error', '/path/to/file', 0);
        $this->assertEquals(0, $exception->getCode());
    }

    public function testFileExceptionWithNegativeCode(): void {
        $exception = new FileException('File error', '/path/to/file', -1);
        $this->assertEquals(-1, $exception->getCode());
    }

    public function testFileExceptionInheritanceChain(): void {
        $exception = new FileException('Error', '/path/to/file');

        $this->assertInstanceOf(FileException::class, $exception);
        $this->assertInstanceOf('Hubbitus\HuPHP\Exceptions\BaseException', $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testFileExceptionCustomToString(): void {
        $exception = new FileException('File error', '/path/to/file');
        $string = $exception->__toString();
        
        $this->assertIsString($string);
        $this->assertStringContainsString('FileException', $string);
        $this->assertStringContainsString('/path/to/file', $string);
        $this->assertStringContainsString('File error', $string);
    }

    public function testFileExceptionToStringFormat(): void {
        $exception = new FileException('Permission denied', '/etc/passwd');
        $string = $exception->__toString();
        
        // Expected format: Hubbitus\HuPHP\Exceptions\Filesystem\FileException: [/etc/passwd]: Permission denied
        $this->assertMatchesRegularExpression('/FileException.*\[\/etc\/passwd\].*Permission denied/', $string);
    }

    public function testFileExceptionToStringWithEmptyMessage(): void {
        $exception = new FileException('', '/path/to/file');
        $string = $exception->__toString();
        
        $this->assertIsString($string);
        $this->assertStringContainsString('/path/to/file', $string);
    }

    public function testFileExceptionGetFullPath(): void {
        $exception = new FileException('Error', '/full/path/to/file.txt');
        $fullPath = $exception->getFullPath();

        $this->assertEquals('/full/path/to/file.txt', $fullPath);
    }

    public function testFileExceptionGetFullPathWithEmptyFilename(): void {
        $exception = new FileException('Error', '');
        $fullPath = $exception->getFullPath();

        $this->assertEquals('', $fullPath);
    }

    public function testFileExceptionGetFullPathIsPublic(): void {
        $reflection = new \ReflectionMethod(FileException::class, 'getFullPath');
        $this->assertTrue($reflection->isPublic());
    }
}
