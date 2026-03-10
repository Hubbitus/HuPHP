<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\System;

use Hubbitus\HuPHP\System\OutputType;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\System\OutputType
**/
class OutputTypeTest extends TestCase {
    public function testWebCaseExists(): void {
        $this->assertEquals(OutputType::WEB, OutputType::WEB);
    }

    public function testConsoleCaseExists(): void {
        $this->assertEquals(OutputType::CONSOLE, OutputType::CONSOLE);
    }

    public function testPrintCaseExists(): void {
        $this->assertEquals(OutputType::PRINT, OutputType::PRINT);
    }

    public function testFileCaseExists(): void {
        $this->assertEquals(OutputType::FILE, OutputType::FILE);
    }

    public function testWapCaseExists(): void {
        $this->assertEquals(OutputType::WAP, OutputType::WAP);
    }

    public function testAllCasesAreUnique(): void {
        $cases = [
            OutputType::WEB,
            OutputType::CONSOLE,
            OutputType::PRINT,
            OutputType::FILE,
            OutputType::WAP,
        ];

        $uniqueCases = array_unique($cases, SORT_REGULAR);

        $this->assertCount(5, $uniqueCases, 'All OutputType cases should be unique');
    }

    public function testCasesCount(): void {
        $this->assertCount(5, OutputType::cases());
    }

    public function testNameProperty(): void {
        $this->assertEquals('WEB', OutputType::WEB->name);
        $this->assertEquals('CONSOLE', OutputType::CONSOLE->name);
        $this->assertEquals('PRINT', OutputType::PRINT->name);
        $this->assertEquals('FILE', OutputType::FILE->name);
        $this->assertEquals('WAP', OutputType::WAP->name);
    }

    public function testIsSameCase(): void {
        $this->assertTrue(OutputType::WEB === OutputType::WEB);
        $this->assertFalse(OutputType::WEB === OutputType::CONSOLE);
    }

    public function testCanBeUsedInSwitch(): void {
        $type = OutputType::CONSOLE;
        $result = match ($type) {
            OutputType::WEB => 'web',
            OutputType::CONSOLE => 'console',
            OutputType::PRINT => 'print',
            OutputType::FILE => 'file',
            OutputType::WAP => 'wap',
        };

        $this->assertEquals('console', $result);
    }

    public function testAllCasesHaveCorrectNames(): void {
        $expectedNames = ['WEB', 'CONSOLE', 'PRINT', 'FILE', 'WAP'];
        $actualNames = array_map(fn(OutputType $case) => $case->name, OutputType::cases());

        $this->assertEquals($expectedNames, $actualNames);
    }
}
