<?php
declare(strict_types=1);

/**
 * Test for HuGetoptArgumentRequiredException class.
 */

namespace Hubbitus\HuPHP\Tests\System\Console;

use Hubbitus\HuPHP\System\Console\HuGetoptArgumentRequiredException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\System\Console\HuGetoptArgumentRequiredException
 */
class HuGetoptArgumentRequiredExceptionTest extends TestCase
{
    public function testClassInstantiation(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $this->assertInstanceOf(HuGetoptArgumentRequiredException::class, $exception);
    }

    public function testClassExtendsVariableRequiredException(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $this->assertInstanceOf('Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException', $exception);
    }

    public function testExceptionMessage(): void {
        $exception = new HuGetoptArgumentRequiredException('Option requires argument');
        $this->assertEquals('Option requires argument', $exception->getMessage());
    }

    public function testExceptionCode(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required', 400);
        $this->assertEquals(400, $exception->getCode());
    }

    public function testExceptionCanBeThrown(): void {
        $this->expectException(HuGetoptArgumentRequiredException::class);
        $this->expectExceptionMessage('Option -f requires an argument');

        throw new HuGetoptArgumentRequiredException('Option -f requires an argument');
    }

    public function testExceptionWithPrevious(): void {
        $previous = new \Exception('Previous error');
        $exception = new HuGetoptArgumentRequiredException('Argument required', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionInheritance(): void {
        $exception = new HuGetoptArgumentRequiredException('Error');
        $this->assertInstanceOf(\Throwable::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionToString(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $string = (string) $exception;
        $this->assertIsString($string);
        $this->assertNotEmpty($string);
    }

    public function testExceptionTrace(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $this->assertIsArray($exception->getTrace());
    }

    public function testExceptionFile(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $this->assertIsString($exception->getFile());
    }

    public function testExceptionLine(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $this->assertIsInt($exception->getLine());
    }

    public function testExceptionSerialize(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $serialized = serialize($exception);
        $this->assertIsString($serialized);
    }

    public function testExceptionJsonEncode(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $json = json_encode($exception);
        $this->assertIsString($json);
    }

    public function testExceptionMultipleInstances(): void {
        $exception1 = new HuGetoptArgumentRequiredException('Error 1');
        $exception2 = new HuGetoptArgumentRequiredException('Error 2');

        $this->assertNotSame($exception1, $exception2);
        $this->assertNotEquals($exception1->getMessage(), $exception2->getMessage());
    }

    public function testExceptionGetObjectId(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $id = spl_object_id($exception);
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testExceptionGetObjectHash(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $hash = spl_object_hash($exception);
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function testExceptionVarDump(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        ob_start();
        var_dump($exception);
        $output = ob_get_clean();
        $this->assertIsString($output);
        $this->assertStringContainsString('HuGetoptArgumentRequiredException', $output);
    }

    public function testExceptionTrueInBooleanContext(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $this->assertTrue((bool) $exception);
    }

    public function testExceptionGetType(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $this->assertEquals('object', gettype($exception));
    }

    public function testExceptionGetClassName(): void {
        $exception = new HuGetoptArgumentRequiredException('Argument required');
        $this->assertEquals('Hubbitus\HuPHP\System\Console\HuGetoptArgumentRequiredException', get_class($exception));
    }

    public function testExceptionInheritanceChain(): void {
        $exception = new HuGetoptArgumentRequiredException('Error');

        $this->assertInstanceOf(HuGetoptArgumentRequiredException::class, $exception);
        $this->assertInstanceOf('Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException', $exception);
        $this->assertInstanceOf('Hubbitus\HuPHP\Exceptions\BaseException', $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }
}
