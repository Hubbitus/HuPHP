<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;
use Hubbitus\HuPHP\System\OutputType;

use Hubbitus\HuPHP\Debug\HuLOGSimpleTestFormatter;
use Hubbitus\HuPHP\Debug\HuLOGText;
use Hubbitus\HuPHP\Debug\HuLOGTextSettings;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Debug\HuLOGSimpleTestFormatter
**/
class HuLOGSimpleTestFormatterTest extends TestCase {
    private HuLOGSimpleTestFormatter $formatter;
    private HuLOGText $logText;

    protected function setUp(): void {
        $this->formatter = new HuLOGSimpleTestFormatter();
        $this->logText = new HuLOGText(new HuLOGTextSettings());
    }

    public function testFormatForFile(): void {
        $this->logText->setSettingsArray([
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
