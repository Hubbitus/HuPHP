<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\BacktraceNode;
use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use Hubbitus\HuPHP\System\OS;
use Hubbitus\HuPHP\System\OutputType;
use PHPUnit\Framework\TestCase;
use Hubbitus\HuPHP\Debug\Format\PrintoutDefault;

/**
* @covers Hubbitus\HuPHP\Debug\BacktraceNode
**/
final class BacktraceNodeTest extends TestCase {
    protected function setUp(): void {
        // Configure default backtrace format
        PrintoutDefault::configure();
    }

    private array $sampleData = [
        'file' => '/test/file.php',
        'line' => 42,
        'function' => 'testFunction',
        'class' => 'TestClass',
        'type' => '->',
        'args' => ['arg1', 'arg2'],
        'N' => 0,
    ];

    public function testConstructorWithArray(): void {
        $node = new BacktraceNode($this->sampleData);

        $this->assertInstanceOf(BacktraceNode::class, $node);
        $this->assertEquals('/test/file.php', $node->file);
        $this->assertEquals(42, $node->line);
    }

    public function testConstructorWithN(): void {
        $node = new BacktraceNode($this->sampleData, 5);

        $this->assertEquals(5, $node->N);
    }

    public function testCreateStaticMethod(): void {
        $node = BacktraceNode::create($this->sampleData, 3);

        $this->assertInstanceOf(BacktraceNode::class, $node);
        $this->assertEquals(3, $node->N);
    }

    public function testGetExistingProperty(): void {
        $node = new BacktraceNode($this->sampleData);

        $this->assertEquals('/test/file.php', $node->file);
        $this->assertEquals(42, $node->line);
        $this->assertEquals('testFunction', $node->function);
        $this->assertEquals('TestClass', $node->class);
        $this->assertEquals('->', $node->type);
        $this->assertEquals(['arg1', 'arg2'], $node->args);
    }

    public function testGetNonExistingPropertyThrowsException(): void {
        $node = new BacktraceNode($this->sampleData);

        $this->expectException(ClassPropertyNotExistsException::class);
        /** @phpstan-ignore-next-line **/
        $node->nonExistingProperty;
    }

    public function testIssetExistingProperty(): void {
        $node = new BacktraceNode($this->sampleData);

        $this->assertTrue(isset($node->file));
        $this->assertTrue(isset($node->line));
    }

    public function testIssetNonExistingPropertyThrowsException(): void {
        $node = new BacktraceNode($this->sampleData);

        $this->expectException(ClassPropertyNotExistsException::class);
        isset($node->nonExistingProperty);
    }

    public function testIteratorInterface(): void {
        $node = new BacktraceNode($this->sampleData);
        $node->rewind();

        $this->assertNotNull($node->current());
        $this->assertNotNull($node->key());
        $this->assertTrue($node->valid());

        $node->next();
        $this->assertNotNull($node->current());
    }

    public function testFnmatchCmpEqual(): void {
        $node1 = new BacktraceNode([
            'file' => '/test/file.php',
            'line' => 42,
            'function' => 'testFunction',
        ]);

        $node2 = new BacktraceNode([
            'file' => '/test/file.php',
            'function' => 'testFunction',
        ]);

        $result = $node1->fnmatchCmp($node2);

        $this->assertEquals(0, $result);
    }

    public function testFnmatchCmpNotEqual(): void {
        $node1 = new BacktraceNode([
            'file' => '/test/file.php',
            'function' => 'testFunction',
        ]);

        $node2 = new BacktraceNode([
            'file' => '/test/other.php',
            'function' => 'testFunction',
        ]);

        $result = $node1->fnmatchCmp($node2);

        $this->assertNotEquals(0, $result);
    }

    public function testFnmatchCmpWithPattern(): void {
        $node1 = new BacktraceNode([
            'file' => '/test/file.php',
            'function' => 'testFunction',
        ]);

        $node2 = new BacktraceNode([
            'file' => '*file.php',
            'function' => 'test*',
        ]);

        $result = $node1->fnmatchCmp($node2);

        $this->assertEquals(0, $result);
    }

    public function testSetArgsFormat(): void {
        $node = new BacktraceNode($this->sampleData);
        $format = [
            OutputType::CONSOLE->name => [
                'integer' => ['v:::'],
                'string' => ['v:::'],
                'default' => ['v:::'],
            ],
        ];

        $node->setArgsFormat($format);

        $this->assertNotNull($node);
    }

    public function testFormatArgs(): void {
        $node = new BacktraceNode([
            'file' => '/test/file.php',
            'line' => 42,
            'function' => 'testFunction',
            'args' => ['arg1', 123],
        ]);

        // Format passed directly to formatArgs should be argtypes format
        $format = [
            'string' => ['v:::'],
            'integer' => ['v:::'],
            'default' => ['v:::'],
        ];

        $result = $node->formatArgs($format);

        $this->assertIsString($result);
        $this->assertStringContainsString('arg1', $result);
        $this->assertStringContainsString('123', $result);
    }

    public function testFormatArgsWithEmptyArgs(): void {
        $node = new BacktraceNode([
            'file' => '/test/file.php',
            'line' => 42,
            'function' => 'testFunction',
            'args' => [],
        ]);

        $format = [
            'default' => ['v:::'],
        ];

        $result = $node->formatArgs($format);

        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    public function testDump(): void {
        $node = new BacktraceNode($this->sampleData);
        $result = $node->dump(true);

        $this->assertIsString($result);
        $this->assertStringContainsString('/test/file.php', $result);
    }

    public function testFormatArgsWithDefaultFormat(): void {
        $node = new BacktraceNode([
            'file' => '/test/file.php',
            'line' => 42,
            'function' => 'testFunction',
            'args' => ['arg1', 123],
        ]);

        $format = [
            'default' => ['v:::'],
        ];

        $result = $node->formatArgs($format);

        $this->assertIsString($result);
        $this->assertStringContainsString('arg1', $result);
        $this->assertStringContainsString('123', $result);
    }

    public function testFormatArgsWithGlobalConfig(): void {
        $node = new BacktraceNode([
            'file' => '/test/file.php',
            'line' => 42,
            'function' => 'testFunction',
            'args' => ['arg1', 123],
        ]);

        // Do NOT set format via setArgsFormat - use global config directly
        // This tests the $GLOBALS['__CONFIG']['backtrace::printout'][$outType]['argtypes'] branch
        $result = $node->formatArgs(null);

        $this->assertIsString($result);
        $this->assertStringContainsString('arg1', $result);
        $this->assertStringContainsString('123', $result);
    }

    public function testFormatArgsWithInstanceFormat(): void {
        $node = new BacktraceNode([
            'file' => '/test/file.php',
            'line' => 42,
            'function' => 'testFunction',
            'args' => ['arg1', 123],
        ]);

        // Set format via setArgsFormat to test $this->_format branch
        $outType = OS::getOutType();
        $format = [
            $outType->name => [
                'argtypes' => [
                    'string' => ['v:::'],
                    'integer' => ['v:::'],
                    'default' => ['v:::'],
                ],
            ],
        ];
        $node->setArgsFormat($format);

        $result = $node->formatArgs(null);

        $this->assertIsString($result);
        $this->assertStringContainsString('arg1', $result);
        $this->assertStringContainsString('123', $result);
    }

    public function testFormatArgsThrowsExceptionForUnknownType(): void {
        $node = new BacktraceNode([
            'file' => '/test/file.php',
            'line' => 42,
            'function' => 'testFunction',
            'args' => [tmpfile()], // resource type
        ]);

        $format = [
            'integer' => ['v:::'],
            // No 'default' and no 'resource'
        ];

        $this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableArrayInconsistentException::class);
        $this->expectExceptionMessage('Format of type resource not found. "default" also not provided in $format');
        $node->formatArgs($format);
    }

    // Note: testFormatArgsThrowsExceptionWithClearMessage is not possible because
    // PrintoutDefault::configure() is automatically called inside formatArgs() to ensure
    // default configuration is always available. This is by design for defensive coding.

    public function testFormatArgsConfiguresPrintoutDefault(): void {
        // Test that formatArgs() calls PrintoutDefault::configure() when global config is not set
        // This covers line 215: PrintoutDefault::configure();
        $debugData = [
            'file' => '/test/file.php',
            'line' => 10,
            'function' => 'testFunction',
            'args' => ['arg1', 'arg2'],
            'N' => 0,
        ];

        $node = new BacktraceNode($debugData, 0);

        // Clear global config to trigger PrintoutDefault::configure()
        $originalConfig = $GLOBALS['__CONFIG']['backtrace::printout'] ?? null;
        unset($GLOBALS['__CONFIG']['backtrace::printout']);

        try {
            $result = $node->formatArgs();
            $this->assertIsString($result);
            $this->assertNotEmpty($result);
        } finally {
            $GLOBALS['__CONFIG']['backtrace::printout'] = $originalConfig;
        }
    }
}
