<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\HuLOGText;
use Hubbitus\HuPHP\Debug\HuLOGTextFormatter;
use Hubbitus\HuPHP\Debug\HuLOGTextSettings;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Debug\HuLOGTextFormatter
**/
class HuLOGTextFormatterTest extends TestCase {
    private HuLOGTextFormatter $formatter;
    private HuLOGText $logText;

    protected function setUp(): void {
        $this->logText = new HuLOGText(new HuLOGTextSettings());
        $this->formatter = new HuLOGTextFormatter();
    }

    public function testConstructor(): void {
        $formatter = new HuLOGTextFormatter();

        $this->assertInstanceOf(HuLOGTextFormatter::class, $formatter);
    }

    public function testFormatForFile(): void {
        $this->logText->setSettingsArray([
            'date' => '2026-02-22 10:00:00',
            'level' => '  ',
            'type' => 'ERR',
            'logText' => 'Test message',
        ]);
        $result = $this->formatter->formatForFile($this->logText);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testFormatForPrint(): void {
        $this->logText->setSettingsArray([
            'date' => '2026-02-22 10:00:00',
            'level' => '  ',
            'type' => 'INFO',
            'logText' => 'Print test',
        ]);
        $result = $this->formatter->formatForPrint($this->logText);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testFormatForFileWithExtra(): void {
        $this->logText->setSettingsArray([
            'date' => '2026-02-22 10:00:00',
            'level' => '  ',
            'type' => 'ERR',
            'logText' => 'Test with extra',
            'extra' => ['key' => 'value'],
        ]);
        $result = $this->formatter->formatForFile($this->logText);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testFormatForPrintWithExtra(): void {
        $this->logText->setSettingsArray([
            'level' => '    ',
            'type' => 'DEBUG',
            'logText' => 'Debug message',
        ]);
        $result = $this->formatter->formatForPrint($this->logText);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }
}
