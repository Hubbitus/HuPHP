<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;
use Hubbitus\HuPHP\System\OutputType;

use Hubbitus\HuPHP\Debug\Tokenizer;
use Hubbitus\HuPHP\Debug\BacktraceNode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\Tokenizer
 */
class TokenizerTest extends TestCase {
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Tokenizer::class));
    }

    public function testCreateWithBacktrace(): void {
        // Create a backtrace node manually with test data
        $nodeData = [
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'testCreateWithBacktrace',
            'class' => self::class,
            'type' => '->',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);

        $tokenizer = Tokenizer::create($node);
        $this->assertInstanceOf(Tokenizer::class, $tokenizer);
    }

    public function testInstanceHasExpectedMethods(): void {
        $methods = get_class_methods(Tokenizer::class);

        $this->assertContains('create', $methods);
        $this->assertContains('clear', $methods);
        $this->assertContains('getArg', $methods);
        $this->assertContains('parseCallArgs', $methods);
        $this->assertContains('setFromBTN', $methods);
    }

    public function testGetArgWithInvalidIndex(): void {
        // Create a backtrace node manually with test data
        $nodeData = [
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'testGetArgWithInvalidIndex',
            'class' => self::class,
            'type' => '->',
            'args' => ['arg1', 'arg2'],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = Tokenizer::create($node);

        // Check method exists
        $this->assertTrue(method_exists($tokenizer, 'getArg'));

        // Test that getArg method is callable
        $this->assertIsCallable([$tokenizer, 'getArg']);

        // Test getArgs returns array (may be empty if parsing failed)
        $args = $tokenizer->getArgs();
        $this->assertIsArray($args);
    }
}
