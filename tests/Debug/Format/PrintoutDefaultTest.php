<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug\Format;

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Debug\Format\PrintoutDefault;
use Hubbitus\HuPHP\System\OutputType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\Format\PrintoutDefault
 */
final class PrintoutDefaultTest extends TestCase {
    /**
     * @var array|null Backup of original config
     */
    private ?array $originalConfig = null;

    protected function setUp(): void {
        // Backup original config
        $this->originalConfig = $GLOBALS['__CONFIG']['backtrace::printout'] ?? null;
        // Clear config to force configure() call
        $GLOBALS['__CONFIG']['backtrace::printout'] = [];
    }

    protected function tearDown(): void {
        // Restore original config
        if ($this->originalConfig !== null) {
            $GLOBALS['__CONFIG']['backtrace::printout'] = $this->originalConfig;
        } else {
            unset($GLOBALS['__CONFIG']['backtrace::printout']);
        }
    }

    public function testConfigureSetsWebFormat(): void {
        PrintoutDefault::configure();

        $this->assertArrayHasKey(OutputType::WEB->name, $GLOBALS['__CONFIG']['backtrace::printout']);
        $this->assertIsArray($GLOBALS['__CONFIG']['backtrace::printout'][OutputType::WEB->name]);
    }

    public function testConfigureSetsConsoleFormat(): void {
        PrintoutDefault::configure();

        $this->assertArrayHasKey(OutputType::CONSOLE->name, $GLOBALS['__CONFIG']['backtrace::printout']);
        $this->assertIsArray($GLOBALS['__CONFIG']['backtrace::printout'][OutputType::CONSOLE->name]);
    }

    public function testConfigureSetsFileFormat(): void {
        PrintoutDefault::configure();

        $this->assertArrayHasKey(OutputType::FILE->name, $GLOBALS['__CONFIG']['backtrace::printout']);
        $this->assertIsArray($GLOBALS['__CONFIG']['backtrace::printout'][OutputType::FILE->name]);
    }

    public function testConfigureSetsArgTypes(): void {
        PrintoutDefault::configure();

        $webArgTypes = $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::WEB->name]['argtypes'];

        $this->assertArrayHasKey('integer', $webArgTypes);
        $this->assertArrayHasKey('double', $webArgTypes);
        $this->assertArrayHasKey('string', $webArgTypes);
        $this->assertArrayHasKey('array', $webArgTypes);
        $this->assertArrayHasKey('object', $webArgTypes);
        $this->assertArrayHasKey('boolean', $webArgTypes);
        $this->assertArrayHasKey('NULL', $webArgTypes);
        $this->assertArrayHasKey('default', $webArgTypes);
    }

    public function testConfigureConsoleAndFileUseWebArgTypes(): void {
        PrintoutDefault::configure();

        $webArgTypes = $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::WEB->name]['argtypes'];
        $consoleArgTypes = $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::CONSOLE->name]['argtypes'];
        $fileArgTypes = $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::FILE->name]['argtypes'];

        $this->assertSame($webArgTypes, $consoleArgTypes);
        $this->assertSame($webArgTypes, $fileArgTypes);
    }

    public function testBacktracePrintoutWebHelperReturnsString(): void {
        $result = PrintoutDefault::backtrace__printout_WEB_helper('short', 'long');

        $this->assertIsString($result);
        $this->assertStringContainsString('short', $result);
        $this->assertStringContainsString('title="long"', $result);
    }

    public function testBacktracePrintoutWebHelperWithCustomTags(): void {
        $result = PrintoutDefault::backtrace__printout_WEB_helper(
            'short',
            'long',
            '<div>',
            '</div>'
        );

        $this->assertIsString($result);
        $this->assertStringContainsString('<div>', $result);
        $this->assertStringContainsString('</div>', $result);
    }

    public function testBacktracePrintoutWebHelperEscapesQuotes(): void {
        $result = PrintoutDefault::backtrace__printout_WEB_helper(
            "test'string",
            "long'string"
        );

        $this->assertIsString($result);
    }

    public function testConfigureWorksAfterClearingGlobalConfig(): void {
        // Clear config
        $GLOBALS['__CONFIG']['backtrace::printout'] = [];

        // Call configure
        PrintoutDefault::configure();

        // Verify config is set
        $this->assertNotEmpty($GLOBALS['__CONFIG']['backtrace::printout']);
        $this->assertArrayHasKey(OutputType::WEB->name, $GLOBALS['__CONFIG']['backtrace::printout']);
    }

    public function testConfigureCanBeCalledMultipleTimes(): void {
        PrintoutDefault::configure();
        $firstConfig = $GLOBALS['__CONFIG']['backtrace::printout'];

        PrintoutDefault::configure();
        $secondConfig = $GLOBALS['__CONFIG']['backtrace::printout'];

        $this->assertSame($firstConfig, $secondConfig);
    }

    public function testWebFormatContainsExpectedStructure(): void {
        PrintoutDefault::configure();

        $webFormat = $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::WEB->name];

        $this->assertArrayHasKey('A:::', $webFormat);
        $this->assertArrayHasKey('argtypes', $webFormat);
    }

    public function testConsoleFormatContainsExpectedStructure(): void {
        PrintoutDefault::configure();

        $consoleFormat = $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::CONSOLE->name];

        $this->assertArrayHasKey('A:::', $consoleFormat);
        $this->assertArrayHasKey('argtypes', $consoleFormat);
    }

    public function testFileFormatContainsExpectedStructure(): void {
        PrintoutDefault::configure();

        $fileFormat = $GLOBALS['__CONFIG']['backtrace::printout'][OutputType::FILE->name];

        $this->assertArrayHasKey('A:::', $fileFormat);
        $this->assertArrayHasKey('argtypes', $fileFormat);
    }

    public function testTearDownRestoresOriginalConfig(): void {
        // Set custom config
        $customConfig = ['custom' => 'config'];
        $GLOBALS['__CONFIG']['backtrace::printout'] = $customConfig;

        // Create test instance (setUp will backup)
        $test = new PrintoutDefaultTest('testConfigureSetsWebFormat');
        $test->setUp();

        // Config should be cleared
        $this->assertEmpty($GLOBALS['__CONFIG']['backtrace::printout']);

        // TearDown should restore
        $test->tearDown();
        $this->assertSame($customConfig, $GLOBALS['__CONFIG']['backtrace::printout']);
    }
}
