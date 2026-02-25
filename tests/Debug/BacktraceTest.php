<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Debug\BacktraceNode;
use Hubbitus\HuPHP\Debug\Format\PrintoutDefault;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\System\OutputType;
use PHPUnit\Framework\TestCase;

/**
 * @covers Hubbitus\HuPHP\Debug\Backtrace
 */
final class BacktraceTest extends TestCase {
    protected function setUp(): void {
        // Configure default backtrace format
        PrintoutDefault::configure();
    }

    public function testConstructorWithNullCreatesBacktrace(): void {
        $bt = new Backtrace(null, 0);

        $this->assertInstanceOf(Backtrace::class, $bt);
        $this->assertGreaterThan(0, $bt->length());
    }

    public function testConstructorWithArray(): void {
        $debugData = [
            ['file' => '/test/file.php', 'line' => 10, 'function' => 'testFunction'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'testFunction2'],
        ];

        $bt = new Backtrace($debugData, 0);

        $this->assertEquals(2, $bt->length());
    }

    public function testConstructorRemoveSelf(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
            ['file' => '/test/file3.php', 'line' => 30, 'function' => 'func3'],
        ];

        $bt = new Backtrace($debugData, 1);

        $this->assertEquals(2, $bt->length());
    }

    public function testCreateStaticMethod(): void {
        $debugData = [
            ['file' => '/test/file.php', 'line' => 10, 'function' => 'testFunction'],
        ];

        $bt = Backtrace::create($debugData, 0);

        $this->assertInstanceOf(Backtrace::class, $bt);
        $this->assertEquals(1, $bt->length());
    }

    public function testGetNode(): void {
        $debugData = [
            ['file' => '/test/file.php', 'line' => 10, 'function' => 'testFunction'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'testFunction2'],
        ];

        $bt = new Backtrace($debugData, 0);
        $node = $bt->getNode(0);

        $this->assertInstanceOf(BacktraceNode::class, $node);
        $this->assertEquals('/test/file.php', $node->file);
        $this->assertEquals(10, $node->line);
    }

    public function testGetNodeWithNegativeIndex(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
            ['file' => '/test/file3.php', 'line' => 30, 'function' => 'func3'],
        ];

        $bt = new Backtrace($debugData, 0);
        $node = $bt->getNode(-1);

        $this->assertInstanceOf(BacktraceNode::class, $node);
        $this->assertEquals('/test/file3.php', $node->file);
    }

    public function testGetNodeWithNullIndex(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
        ];

        $bt = new Backtrace($debugData, 0);
        $node = $bt->getNode(null);

        $this->assertInstanceOf(BacktraceNode::class, $node);
        $this->assertEquals('/test/file1.php', $node->file);
    }

    public function testGetNodeThrowsExceptionForInvalidIndex(): void {
        $debugData = [
            ['file' => '/test/file.php', 'line' => 10, 'function' => 'testFunction'],
        ];

        $bt = new Backtrace($debugData, 0);

        $this->expectException(VariableRangeException::class);
        $bt->getNode(100);
    }

    public function testSetNode(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
        ];

        $bt = new Backtrace($debugData, 0);
        $newNode = new BacktraceNode(['file' => '/new/file.php', 'line' => 99, 'function' => 'newFunc'], 0);

        $bt->setNode(0, $newNode);
        $node = $bt->getNode(0);

        $this->assertEquals('/new/file.php', $node->file);
        $this->assertEquals(99, $node->line);
    }

    public function testDelNode(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
            ['file' => '/test/file3.php', 'line' => 30, 'function' => 'func3'],
        ];

        $bt = new Backtrace($debugData, 0);
        $bt->delNode(1);

        $this->assertEquals(2, $bt->length());
        $this->assertEquals('/test/file1.php', $bt->getNode(0)->file);
        $this->assertEquals('/test/file3.php', $bt->getNode(1)->file);
    }

    public function testDelNodeWithNegativeIndex(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
            ['file' => '/test/file3.php', 'line' => 30, 'function' => 'func3'],
        ];

        $bt = new Backtrace($debugData, 0);
        $bt->delNode(-1);

        $this->assertEquals(2, $bt->length());
        // After deleting last element (-1), index 1 points to file2.php
        $this->assertEquals('/test/file2.php', $bt->getNode(1)->file);
    }

    public function testDelNodeThrowsExceptionForInvalidIndex(): void {
        $debugData = [
            ['file' => '/test/file.php', 'line' => 10, 'function' => 'func1'],
        ];

        $bt = new Backtrace($debugData, 0);

        $this->expectException(VariableRangeException::class);
        $bt->delNode(100);
    }

    public function testLength(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
            ['file' => '/test/file3.php', 'line' => 30, 'function' => 'func3'],
        ];

        $bt = new Backtrace($debugData, 0);

        $this->assertEquals(3, $bt->length());
    }

    public function testIteratorInterface(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
        ];

        $bt = new Backtrace($debugData, 0);
        $bt->rewind();

        $this->assertEquals(0, $bt->key());
        $this->assertInstanceOf(BacktraceNode::class, $bt->current());
        $this->assertTrue($bt->valid());

        $bt->next();
        $this->assertEquals(1, $bt->key());
        $this->assertInstanceOf(BacktraceNode::class, $bt->current());

        $bt->next();
        $this->assertFalse($bt->valid());
        $this->assertNull($bt->current());
    }

    public function testEndMethod(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
            ['file' => '/test/file3.php', 'line' => 30, 'function' => 'func3'],
        ];

        $bt = new Backtrace($debugData, 0);
        $node = $bt->end();

        $this->assertInstanceOf(BacktraceNode::class, $node);
        $this->assertEquals('/test/file3.php', $node->file);
    }

    public function testPrevMethod(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
            ['file' => '/test/file3.php', 'line' => 30, 'function' => 'func3'],
        ];

        $bt = new Backtrace($debugData, 0);
        $bt->end();

        $node = $bt->prev();
        $this->assertInstanceOf(BacktraceNode::class, $node);
        $this->assertEquals('/test/file2.php', $node->file);

        $node = $bt->prev();
        $this->assertInstanceOf(BacktraceNode::class, $node);
        $this->assertEquals('/test/file1.php', $node->file);

        $node = $bt->prev();
        $this->assertNull($node);
    }

    public function testFindMethod(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'testFunc'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'otherFunc'],
            ['file' => '/test/file3.php', 'line' => 30, 'function' => 'testFunc'],
        ];

        $bt = new Backtrace($debugData, 0);
        $searchNode = new BacktraceNode(['function' => 'testFunc']);
        $found = $bt->find($searchNode);

        $this->assertInstanceOf(Backtrace::class, $found);
        $this->assertEquals(2, $found->length());
    }

    public function testFindMethodWithFilePattern(): void {
        $debugData = [
            ['file' => '/test/file1.php', 'line' => 10, 'function' => 'func1'],
            ['file' => '/test/file2.php', 'line' => 20, 'function' => 'func2'],
        ];

        $bt = new Backtrace($debugData, 0);
        $searchNode = new BacktraceNode(['file' => '*file1.php']);
        $found = $bt->find($searchNode);

        $this->assertEquals(1, $found->length());
        $this->assertEquals('/test/file1.php', $found->getNode(0)->file);
    }

    /**
     * Test printFormat with empty backtrace.
     */
    public function testPrintFormatWithEmptyBacktrace(): void {
        // Configure simple default format - print "empty" for empty backtrace
        $GLOBALS['__CONFIG']['backtrace::printout'] = [
            OutputType::CONSOLE->name => ['v:::empty backtrace'],
        ];

        $bt = new Backtrace([], 0);
        $result = $bt->printFormat();

        $this->assertIsString($result);
        $this->assertStringContainsString('Backtrace', $result);
        $this->assertStringContainsString('0 calls', $result);
    }

    /**
     * @todo Fix infinite loop in HuFormat when printing backtrace
     */
    public function testToString(): void {
        $debugData = [
            [
                'file' => '/test/file.php',
                'line' => 10,
                'function' => 'testFunction',
                'args' => [],
            ],
        ];

        $bt = new Backtrace($debugData, 0);
        // Use explicit format to avoid issues with global configuration
        $result = $bt->printFormat(['v:::']);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testSetPrintoutFormat(): void {
        $debugData = [
            ['file' => '/test/file.php', 'line' => 10, 'function' => 'testFunction'],
        ];

        $bt = new Backtrace($debugData, 0);
        $format = [OutputType::CONSOLE->name => ['A:::', ['v']]];

        $result = $bt->setPrintoutFormat($format);

        $this->assertSame($bt, $result);
    }

    public function testDump(): void
    {
        $debugData = [
            ['file' => '/test/file.php', 'line' => 10, 'function' => 'testFunction'],
        ];

        $bt = new Backtrace($debugData, 0);
        $result = $bt->dump(true);

        $this->assertIsString($result);
        $this->assertStringContainsString('/test/file.php', $result);
    }

    public function testFindRegexpThrowsException(): void
    {
        $debugData = [
            ['file' => '/test/file.php', 'line' => 10, 'function' => 'testFunction'],
        ];

        $bt = new Backtrace($debugData, 0);
        $searchNode = new BacktraceNode(['function' => 'testFunction']);

        $this->expectException(\Hubbitus\HuPHP\Exceptions\BaseException::class);
        $bt->findRegexp($searchNode);
    }

    public function testPrintFormatConfiguresDefaultFormat(): void
    {
        $debugData = [
            ['file' => '/test/file.php', 'line' => 10, 'function' => 'testFunction', 'args' => []],
        ];

        $bt = new Backtrace($debugData, 0);
        // Clear global config to trigger PrintoutDefault::configure()
        $originalConfig = $GLOBALS['__CONFIG']['backtrace::printout'] ?? null;
        $GLOBALS['__CONFIG']['backtrace::printout'] = [];

        try {
            $result = $bt->printFormat();
            $this->assertIsString($result);
            $this->assertNotEmpty($result);
        } finally {
            $GLOBALS['__CONFIG']['backtrace::printout'] = $originalConfig;
        }
    }
}
