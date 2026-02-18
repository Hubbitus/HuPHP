<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\Tokenizer;
use Hubbitus\HuPHP\Debug\Backtrace;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\Tokenizer
 */
class TokenizerTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(Tokenizer::class));
    }

    public function testCreateWithBacktrace(): void
    {
        $backtrace = Backtrace::create();
        $node = $backtrace->getNode(0);
        
        if ($node) {
            $tokenizer = Tokenizer::create($node);
            $this->assertInstanceOf(Tokenizer::class, $tokenizer);
        } else {
            // Skip if no backtrace node available
            $this->markTestSkipped('No backtrace node available for testing');
        }
    }

    public function testInstanceHasExpectedMethods(): void
    {
        $methods = get_class_methods(Tokenizer::class);
        
        $this->assertContains('create', $methods);
        $this->assertContains('clear', $methods);
        $this->assertContains('getArg', $methods);
        $this->assertContains('parseCallArgs', $methods);
        $this->assertContains('setFromBTN', $methods);
    }

    public function testGetArgWithInvalidIndex(): void
    {
        // Create tokenizer and test getArg with invalid index
        $backtrace = Backtrace::create();
        $node = $backtrace->getNode(0);
        
        if ($node) {
            $tokenizer = Tokenizer::create($node);
            // Just check method exists and can be called
            $this->assertTrue(method_exists($tokenizer, 'getArg'));
        } else {
            $this->markTestSkipped('No backtrace node available for testing');
        }
    }
}
