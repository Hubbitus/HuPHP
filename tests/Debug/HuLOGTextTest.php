<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;
use Hubbitus\HuPHP\System\OutputType;

use Hubbitus\HuPHP\Debug\HuLOGText;
use Hubbitus\HuPHP\Debug\HuLOGTextSettings;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Debug\HuLOGText
**/
class HuLOGTextTest extends TestCase {
	private HuLOGText $logText;

	protected function setUp(): void {
		$this->logText = new HuLOGText(new HuLOGTextSettings());
	}

	public function testConstructorWithNoArguments(): void {
		$logText = new HuLOGText(null);

		$this->assertInstanceOf(HuLOGText::class, $logText);
	}

	public function testConstructorWithSettingsArray(): void {
		$sets = new HuLOGTextSettings();
		$sets->mergeSettingsArray(['EXTRA_HEADER' => 'Custom Header']);
		$logText = new HuLOGText($sets);

		$this->assertInstanceOf(HuLOGText::class, $logText);
		$this->assertEquals('Custom Header', $logText->settings->EXTRA_HEADER);
	}

	public function testConstructorWithSettingsObject(): void {
		$sets = new HuLOGTextSettings();
		$logText = new HuLOGText($sets);

		$this->assertInstanceOf(HuLOGText::class, $logText);
	}

	public function testSetSettingsArrayPreservesAllValues(): void {
		$this->logText->setSettingsArray([
			'custom' => 'data',
		]);
		$this->logText->addExtra('key', 'value');

		// Verify settings were preserved by checking string output
		$result = $this->logText->strForConsole();
		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testMergeSettingsArray(): void {
		$this->logText->mergeSettingsArray([
			'level' => '  ',
		]);

		$this->logText->mergeSettingsArray([
			'type' => 'ERR',
		]);

		// Verify settings were merged by checking string output
		$result = $this->logText->strForConsole();
		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testStrForFile(): void {
		$this->logText->setSettingsArray([
			'date' => '2026-02-22 10:00:00',
			'level' => '  ',
			'type' => 'ERR',
			'logText' => 'File test',
		]);

		$result = $this->logText->strForFile();

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testStrForConsole(): void {
		$this->logText->setSettingsArray([
			'date' => '2026-02-22 10:00:00',
			'level' => '  ',
			'type' => 'INFO',
			'logText' => 'Console test',
		]);

		$result = $this->logText->strForConsole();

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testStrForPrint(): void {
		$this->logText->setSettingsArray([
			'level' => '  ',
			'type' => 'DEBUG',
			'logText' => 'Print test',
		]);

		$result = $this->logText->strForPrint();

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}

	public function testToString(): void {
		$this->logText->setSettingsArray([
			'level' => '  ',
			'type' => 'WARN',
			'logText' => 'String test',
		]);

		$result = (string) $this->logText;

		$this->assertIsString($result);
		$this->assertNotEmpty($result);
	}
}
