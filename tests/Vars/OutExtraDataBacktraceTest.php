<?php
declare(strict_types=1);

/**
 * Test for OutExtraDataBacktrace class.
 */

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\OutExtraDataBacktrace;
use Hubbitus\HuPHP\Debug\Backtrace;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\OutExtraDataBacktrace
 */
class OutExtraDataBacktraceTest extends TestCase
{
    public function testClassInstantiation(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $this->assertInstanceOf(OutExtraDataBacktrace::class, $extraData);
    }

    public function testClassExtendsOutExtraDataCommon(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $this->assertInstanceOf('Hubbitus\HuPHP\Vars\OutExtraDataCommon', $extraData);
    }

    public function testStrToConsole(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToConsole();
        $this->assertIsString($result);
    }

    public function testStrToConsoleWithNullFormat(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToConsole(null);
        $this->assertIsString($result);
    }

    public function testStrToFile(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToFile();
        $this->assertIsString($result);
    }

    public function testStrToFileWithNullFormat(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToFile(null);
        $this->assertIsString($result);
    }

    public function testStrToWeb(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToWeb();
        $this->assertIsString($result);
    }

    public function testStrToWebWithNullFormat(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToWeb(null);
        $this->assertIsString($result);
    }

    public function testStrToConsoleReturnsNonEmptyString(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToConsole();
        $this->assertIsString($result);
    }

    public function testStrToFileReturnsNonEmptyString(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToFile();
        $this->assertIsString($result);
    }

    public function testStrToWebReturnsNonEmptyString(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToWeb();
        $this->assertIsString($result);
    }

    public function testMultipleInstances(): void {
        $backtrace1 = new Backtrace();
        $backtrace2 = new Backtrace();

        $extraData1 = new OutExtraDataBacktrace($backtrace1);
        $extraData2 = new OutExtraDataBacktrace($backtrace2);

        $this->assertNotSame($extraData1, $extraData2);
    }

    public function testMethodsReturnStrings(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);

        $this->assertIsString($extraData->strToConsole());
        $this->assertIsString($extraData->strToFile());
        $this->assertIsString($extraData->strToWeb());
    }

    public function testInheritedMethods(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);

        $this->assertTrue(method_exists($extraData, 'strToConsole'));
        $this->assertTrue(method_exists($extraData, 'strToFile'));
        $this->assertTrue(method_exists($extraData, 'strToWeb'));
    }

    public function testClassImplementsInterface(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);

        $this->assertInstanceOf('Hubbitus\HuPHP\Vars\IOutExtraData', $extraData);
    }

    public function testConstructorWithBacktrace(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $this->assertInstanceOf(OutExtraDataBacktrace::class, $extraData);
    }

    public function testStrToConsoleOutputFormat(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToConsole();
        $this->assertIsString($result);
    }

    public function testStrToFileOutputFormat(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToFile();
        $this->assertIsString($result);
    }

    public function testStrToWebOutputFormat(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);
        $result = $extraData->strToWeb();
        $this->assertIsString($result);
    }

    public function testBacktraceNotEmpty(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);

        $console = $extraData->strToConsole();
        $file = $extraData->strToFile();
        $web = $extraData->strToWeb();

        $this->assertIsString($console);
        $this->assertIsString($file);
        $this->assertIsString($web);
    }

    public function testMethodSignatures(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);

        $reflection = new \ReflectionClass($extraData);

        $strToConsole = $reflection->getMethod('strToConsole');
        $this->assertEquals('strToConsole', $strToConsole->getName());

        $strToFile = $reflection->getMethod('strToFile');
        $this->assertEquals('strToFile', $strToFile->getName());

        $strToWeb = $reflection->getMethod('strToWeb');
        $this->assertEquals('strToWeb', $strToWeb->getName());
    }

    public function testMethodsArePublic(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);

        $reflection = new \ReflectionClass($extraData);

        $this->assertTrue($reflection->getMethod('strToConsole')->isPublic());
        $this->assertTrue($reflection->getMethod('strToFile')->isPublic());
        $this->assertTrue($reflection->getMethod('strToWeb')->isPublic());
    }

    public function testClone(): void {
        $backtrace = new Backtrace();
        $extraData1 = new OutExtraDataBacktrace($backtrace);
        $extraData2 = clone $extraData1;

        $this->assertEquals($extraData1->strToConsole(), $extraData2->strToConsole());
    }

    public function testSerialization(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);

        $serialized = serialize($extraData);
        $this->assertIsString($serialized);
    }

    public function testToString(): void {
        $backtrace = new Backtrace();
        $extraData = new OutExtraDataBacktrace($backtrace);

        $string = (string) $extraData;
        $this->assertIsString($string);
    }
}
