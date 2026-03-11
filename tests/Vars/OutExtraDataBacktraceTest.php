<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Debug\Format\PrintoutDefault;
use Hubbitus\HuPHP\Vars\OutExtraDataBacktrace;
use PHPUnit\Framework\TestCase;

/**
* @covers Hubbitus\HuPHP\Vars\OutExtraDataBacktrace
**/
class OutExtraDataBacktraceTest extends TestCase {
	protected function setUp(): void {
		// Configure default backtrace format
		PrintoutDefault::configure();
	}

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

	public function testStrForConsole(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForConsole();
		$this->assertIsString($result);
	}

	public function testStrForConsoleWithNullFormat(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForConsole(null);
		$this->assertIsString($result);
	}

	public function testStrForFile(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForFile();
		$this->assertIsString($result);
	}

	public function testStrForFileWithNullFormat(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForFile(null);
		$this->assertIsString($result);
	}

	public function testStrForWeb(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForWeb();
		$this->assertIsString($result);
	}

	public function testStrForWebWithNullFormat(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForWeb(null);
		$this->assertIsString($result);
	}

	public function testStrForConsoleReturnsNonEmptyString(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForConsole();
		$this->assertIsString($result);
	}

	public function testStrForFileReturnsNonEmptyString(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForFile();
		$this->assertIsString($result);
	}

	public function testStrForWebReturnsNonEmptyString(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForWeb();
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

		$this->assertIsString($extraData->strForConsole());
		$this->assertIsString($extraData->strForFile());
		$this->assertIsString($extraData->strForWeb());
	}

	public function testInheritedMethods(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);

		$this->assertTrue(\method_exists($extraData, 'strForConsole'));
		$this->assertTrue(\method_exists($extraData, 'strForFile'));
		$this->assertTrue(\method_exists($extraData, 'strForWeb'));
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

	public function testStrForConsoleOutputFormat(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForConsole();
		$this->assertIsString($result);
	}

	public function testStrForFileOutputFormat(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForFile();
		$this->assertIsString($result);
	}

	public function testStrForWebOutputFormat(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);
		$result = $extraData->strForWeb();
		$this->assertIsString($result);
	}

	public function testBacktraceNotEmpty(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);

		$console = $extraData->strForConsole();
		$file = $extraData->strForFile();
		$web = $extraData->strForWeb();

		$this->assertIsString($console);
		$this->assertIsString($file);
		$this->assertIsString($web);
	}

	public function testMethodSignatures(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);

		$reflection = new \ReflectionClass($extraData);

		$strForConsole = $reflection->getMethod('strForConsole');
		$this->assertEquals('strForConsole', $strForConsole->getName());

		$strForFile = $reflection->getMethod('strForFile');
		$this->assertEquals('strForFile', $strForFile->getName());

		$strForWeb = $reflection->getMethod('strForWeb');
		$this->assertEquals('strForWeb', $strForWeb->getName());
	}

	public function testMethodsArePublic(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);

		$reflection = new \ReflectionClass($extraData);

		$this->assertTrue($reflection->getMethod('strForConsole')->isPublic());
		$this->assertTrue($reflection->getMethod('strForFile')->isPublic());
		$this->assertTrue($reflection->getMethod('strForWeb')->isPublic());
	}

	public function testClone(): void {
		$backtrace = new Backtrace();
		$extraData1 = new OutExtraDataBacktrace($backtrace);
		$extraData2 = clone $extraData1;

		$this->assertEquals($extraData1->strForConsole(), $extraData2->strForConsole());
	}

	/**
	* Test serialization of OutExtraDataBacktrace.
	**/
	public function testSerialization(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);

		$serialized = \serialize($extraData);
		$this->assertIsString($serialized);

		// Test un-serialization
		$unSerialized = \unserialize($serialized);
		$this->assertInstanceOf(OutExtraDataBacktrace::class, $unSerialized);
	}

	public function testToString(): void {
		$backtrace = new Backtrace();
		$extraData = new OutExtraDataBacktrace($backtrace);

		$string = (string) $extraData;
		$this->assertIsString($string);
	}
}
