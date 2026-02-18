<?php
declare(strict_types=1);

/**
 * Test for Backtrace class.
 */

namespace Hubbitus\HuPHP\Tests\Debug;

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Debug\BacktraceNode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\Backtrace
 */
class BacktraceTest extends TestCase
{
    public function testClassInstantiation(): void
    {
        $backtrace = new Backtrace();
        $this->assertInstanceOf(Backtrace::class, $backtrace);
    }

    public function testBacktraceIsEmptyByDefault(): void
    {
        $backtrace = new Backtrace();
        $this->assertEmpty($backtrace->getArray());
    }

    public function testBacktraceCount(): void
    {
        $backtrace = new Backtrace();
        $this->assertEquals(0, $backtrace->count());
    }

    public function testBacktraceWithIgnoreCount(): void
    {
        $backtrace = new Backtrace(1);
        $this->assertInstanceOf(Backtrace::class, $backtrace);
    }

    public function testBacktraceNodeCreation(): void
    {
        $backtrace = new Backtrace();
        $array = $backtrace->getArray();
        $this->assertIsArray($array);
    }

    public function testBacktraceIterator(): void
    {
        $backtrace = new Backtrace();
        $this->assertInstanceOf(\Iterator::class, $backtrace);
    }

    public function testBacktraceRewind(): void
    {
        $backtrace = new Backtrace();
        $backtrace->rewind();
        $this->assertEquals(0, $backtrace->key());
    }

    public function testBacktraceValid(): void
    {
        $backtrace = new Backtrace();
        $this->assertFalse($backtrace->valid());
    }

    public function testBacktraceNext(): void
    {
        $backtrace = new Backtrace();
        $backtrace->next();
        $this->assertEquals(0, $backtrace->key());
    }

    public function testBacktraceKey(): void
    {
        $backtrace = new Backtrace();
        $this->assertEquals(0, $backtrace->key());
    }

    public function testBacktraceCurrent(): void
    {
        $backtrace = new Backtrace();
        $current = $backtrace->current();
        $this->assertNull($current);
    }

    public function testBacktraceToString(): void
    {
        $backtrace = new Backtrace();
        $string = (string) $backtrace;
        $this->assertIsString($string);
    }

    public function testBacktraceGetIterator(): void
    {
        $backtrace = new Backtrace();
        $iterator = $backtrace->getIterator();
        $this->assertInstanceOf(\Iterator::class, $iterator);
    }

    public function testBacktraceWithDifferentIgnoreCounts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $backtrace = new Backtrace($i);
            $this->assertInstanceOf(Backtrace::class, $backtrace);
        }
    }

    public function testBacktraceNodeProperties(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $this->assertInstanceOf(BacktraceNode::class, $node);
    }

    public function testBacktraceNodeGetFile(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $this->assertEquals(__FILE__, $node->file);
    }

    public function testBacktraceNodeGetLine(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => 123,
            'function' => 'test',
            'N' => 0
        ]);
        $this->assertEquals(123, $node->line);
    }

    public function testBacktraceNodeGetFunction(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'testFunction',
            'N' => 0
        ]);
        $this->assertEquals('testFunction', $node->function);
    }

    public function testBacktraceNodeGetClass(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'class' => 'TestClass',
            'N' => 0
        ]);
        $this->assertEquals('TestClass', $node->class);
    }

    public function testBacktraceNodeGetType(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'type' => '->',
            'N' => 0
        ]);
        $this->assertEquals('->', $node->type);
    }

    public function testBacktraceNodeGetArgs(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'args' => ['arg1', 'arg2'],
            'N' => 0
        ]);
        $this->assertEquals(['arg1', 'arg2'], $node->args);
    }

    public function testBacktraceNodeGetN(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 5
        ]);
        $this->assertEquals(5, $node->N);
    }

    public function testBacktraceNodeIterator(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $this->assertInstanceOf(\Iterator::class, $node);
    }

    public function testBacktraceNodeRewind(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $node->rewind();
        $this->assertEquals(0, $node->key());
    }

    public function testBacktraceNodeValid(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $this->assertTrue($node->valid());
    }

    public function testBacktraceNodeNext(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $node->next();
        $this->assertEquals(1, $node->key());
    }

    public function testBacktraceNodeKey(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $key = $node->key();
        $this->assertIsInt($key);
    }

    public function testBacktraceNodeCurrent(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $current = $node->current();
        $this->assertNotNull($current);
    }

    public function testBacktraceNodeToArray(): void
    {
        $data = [
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ];
        $node = new BacktraceNode($data);
        $array = $node->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('file', $array);
        $this->assertArrayHasKey('line', $array);
        $this->assertArrayHasKey('function', $array);
    }

    public function testBacktraceNodeHasProperty(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $this->assertTrue($node->hasProperty('file'));
        $this->assertFalse($node->hasProperty('nonexistent'));
    }

    public function testBacktraceNodeGetProperty(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $this->assertEquals(__FILE__, $node->getProperty('file'));
        $this->assertNull($node->getProperty('nonexistent'));
    }

    public function testBacktraceNodeSetProperty(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $node->setProperty('custom', 'value');
        $this->assertEquals('value', $node->getProperty('custom'));
    }

    public function testBacktraceNodeToString(): void
    {
        $node = new BacktraceNode([
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'test',
            'N' => 0
        ]);
        $string = (string) $node;
        $this->assertIsString($string);
    }
}
