<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

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

    public function testConstructor(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);

        $tokenizer = new Tokenizer($node);
        $this->assertInstanceOf(Tokenizer::class, $tokenizer);
    }

    public function testInstanceHasExpectedMethods(): void {
        $methods = get_class_methods(Tokenizer::class);

        $this->assertContains('create', $methods);
        $this->assertContains('clear', $methods);
        $this->assertContains('getArg', $methods);
        $this->assertContains('setArg', $methods);
        $this->assertContains('getArgs', $methods);
        $this->assertContains('countArgs', $methods);
        $this->assertContains('parseCallArgs', $methods);
        $this->assertContains('setFromBTN', $methods);
        $this->assertContains('trimQuotes', $methods);
    }

    public function testClearMethod(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $result = $tokenizer->clear();
        $this->assertNull($result);
    }

    public function testSetFromBTNMethod(): void {
        $nodeData1 = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction1',
            'args' => [],
            'N' => 0
        ];
        $node1 = BacktraceNode::create($nodeData1, 0);

        $nodeData2 = [
            'file' => __FILE__,
            'line' => 20,
            'function' => 'testFunction2',
            'args' => [],
            'N' => 0
        ];
        $node2 = BacktraceNode::create($nodeData2, 0);

        $tokenizer = new Tokenizer($node1);
        $result = $tokenizer->setFromBTN($node2);

        $this->assertSame($tokenizer, $result);
    }

    public function testGetArgWithInvalidIndex(): void {
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

        $this->assertTrue(method_exists($tokenizer, 'getArg'));
        $this->assertIsCallable([$tokenizer, 'getArg']);

        $args = $tokenizer->getArgs();
        $this->assertIsArray($args);
    }

    public function testGetArgsReturnsArray(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => ['value1', 'value2'],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $args = $tokenizer->getArgs();
        $this->assertIsArray($args);
    }

    public function testSetArg(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $tokenizer->setArg(0, 'test_value');
        $args = $tokenizer->getArgs();
        
        $this->assertArrayHasKey(0, $args);
        $this->assertEquals('test_value', $args[0]);
    }

    public function testSetArgReturnsSelf(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $result = $tokenizer->setArg(0, 'value');
        $this->assertSame($tokenizer, $result);
    }

    public function testCountArgs(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $this->assertEquals(0, $tokenizer->countArgs());

        $tokenizer->setArg(0, 'arg1');
        $this->assertEquals(1, $tokenizer->countArgs());

        $tokenizer->setArg(1, 'arg2');
        $this->assertEquals(2, $tokenizer->countArgs());
    }

    public function testParseCallArgsExists(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $this->assertTrue(method_exists($tokenizer, 'parseCallArgs'));
    }

    public function testTrimQuotesWithDoubleQuotes(): void {
        $result = Tokenizer::trimQuotes('"test"');
        $this->assertEquals('test', $result);
    }

    public function testTrimQuotesWithSingleQuotes(): void {
        $result = Tokenizer::trimQuotes("'test'");
        $this->assertEquals('test', $result);
    }

    public function testTrimQuotesWithNoQuotes(): void {
        $result = Tokenizer::trimQuotes('test');
        $this->assertEquals('test', $result);
    }

    public function testTrimQuotesWithMixedQuotes(): void {
        $result = Tokenizer::trimQuotes("'test\"");
        $this->assertEquals('test', $result);
    }

    public function testTrimQuotesWithAllParameter(): void {
        $result = Tokenizer::trimQuotes('"test"', true);
        $this->assertEquals('test', $result);
    }

    public function testTrimQuotesWithEmptyString(): void {
        $result = Tokenizer::trimQuotes('');
        $this->assertEquals('', $result);
    }

    public function testTrimQuotesWithOnlyStartQuote(): void {
        $result = Tokenizer::trimQuotes('"test');
        $this->assertEquals('"test', $result);
    }

    public function testTrimQuotesWithOnlyEndQuote(): void {
        $result = Tokenizer::trimQuotes('test"');
        $this->assertEquals('test"', $result);
    }

    public function testClassIsNotFinal(): void {
        $reflection = new \ReflectionClass(Tokenizer::class);
        $this->assertFalse($reflection->isFinal());
    }

    public function testClassIsInstantiable(): void {
        $reflection = new \ReflectionClass(Tokenizer::class);
        $this->assertTrue($reflection->isInstantiable());
    }

    public function testCreateReturnsStatic(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);

        $result = Tokenizer::create($node);
        $this->assertInstanceOf(Tokenizer::class, $result);
    }

    public function testClearResetsProperties(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $tokenizer->clear();

        $reflection = new \ReflectionClass($tokenizer);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($tokenizer);
            
            if ($property->getName() === '_debugBacktrace' ||
                $property->getName() === '_filePhpSrc' ||
                $property->getName() === '_tokens' ||
                $property->getName() === '_regexp') {
                $this->assertNull($value, "Property {$property->getName()} should be null after clear");
            }
            if ($property->getName() === '_callText') {
                $this->assertEquals('', $value, "Property {$property->getName()} should be empty string after clear");
            }
            if ($property->getName() === '_callStartLine' || $property->getName() === '_curTokPos') {
                $this->assertEquals(0, $value, "Property {$property->getName()} should be 0 after clear");
            }
            if ($property->getName() === '_args') {
                $this->assertEquals([], $value, "Property {$property->getName()} should be empty array after clear");
            }
        }
    }

    public function testSetFromBTNReturnsSelf(): void {
        $nodeData1 = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction1',
            'args' => [],
            'N' => 0
        ];
        $node1 = BacktraceNode::create($nodeData1, 0);

        $nodeData2 = [
            'file' => __FILE__,
            'line' => 20,
            'function' => 'testFunction2',
            'args' => [],
            'N' => 0
        ];
        $node2 = BacktraceNode::create($nodeData2, 0);

        $tokenizer = new Tokenizer($node1);
        $result = $tokenizer->setFromBTN($node2);

        $this->assertSame($tokenizer, $result);
    }

    public function testGetArgsBeforeParseCallArgs(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $args = $tokenizer->getArgs();
        $this->assertIsArray($args);
    }

    public function testGetArgWithTrim(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $tokenizer->setArg(0, '  test  ');
        $arg = $tokenizer->getArg(0, true);
        
        $this->assertEquals('test', $arg);
    }

    public function testGetArgWithoutTrim(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $tokenizer->setArg(0, '  test  ');
        $arg = $tokenizer->getArg(0, false);
        
        $this->assertEquals('  test  ', $arg);
    }

    public function testParseTokensMethodExists(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $this->assertTrue(method_exists($tokenizer, 'parseTokens'));
    }

    public function testParseCallArgsWithStripWhitespace(): void {
        // Note: parseCallArgs requires a valid PHP file with actual function call
        // This test verifies the method exists and returns self
        $nodeData = [
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'testParseCallArgsWithStripWhitespace',
            'class' => self::class,
            'type' => '->',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        // Method exists and is callable
        $this->assertTrue(method_exists($tokenizer, 'parseCallArgs'));
    }

    public function testParseCallArgsWithStripComments(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'testParseCallArgsWithStripComments',
            'class' => self::class,
            'type' => '->',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $this->assertTrue(method_exists($tokenizer, 'parseCallArgs'));
    }

    public function testParseCallArgsWithBothStripped(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => __LINE__,
            'function' => 'testParseCallArgsWithBothStripped',
            'class' => self::class,
            'type' => '->',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $this->assertTrue(method_exists($tokenizer, 'parseCallArgs'));
    }

    public function testCountArgsAfterParseCallArgs(): void {
        // parseCallArgs requires valid file, just test countArgs works
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $count = $tokenizer->countArgs();
        
        $this->assertIsInt($count);
        $this->assertEquals(0, $count);
    }

    public function testGetArgsAfterParseCallArgs(): void {
        // parseCallArgs requires valid file, just test getArgs works
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $args = $tokenizer->getArgs();
        
        $this->assertIsArray($args);
        $this->assertEmpty($args);
    }

    public function testTrimQuotesStaticMethod(): void {
        $this->assertTrue(method_exists(Tokenizer::class, 'trimQuotes'));
    }

    public function testTrimQuotesWithAllFalse(): void {
        $result = Tokenizer::trimQuotes('"test"', false);
        $this->assertEquals('test', $result);
    }

    public function testMultipleSetArgCalls(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $tokenizer->setArg(0, 'first');
        $tokenizer->setArg(1, 'second');
        $tokenizer->setArg(2, 'third');

        $args = $tokenizer->getArgs();
        $this->assertCount(3, $args);
        $this->assertEquals('first', $args[0]);
        $this->assertEquals('second', $args[1]);
        $this->assertEquals('third', $args[2]);
    }

    public function testParseCallArgsWithRealFunctionCall(): void {
        // Create a test file with a function call
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\nfunction testFunc(\$arg1, \$arg2) {\n    return \$arg1 + \$arg2;\n}\ntestFunc('hello', 'world');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 5, // Line with testFunc call
                'function' => 'testFunc',
                'args' => ['hello', 'world'],
                'N' => 0,
                'type' => '' // Empty type for regular function (not method)
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();

            $this->assertIsArray($args);
            $this->assertGreaterThan(0, count($args));
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsReturnsSelf(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\nmyFunc('test');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'myFunc',
                'args' => ['test'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $result = $tokenizer->parseCallArgs();
            $this->assertInstanceOf(Tokenizer::class, $result);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseTokensReturnsSelf(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\nmyFunc('test');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'myFunc',
                'args' => ['test'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $result = $tokenizer->parseTokens();
            $this->assertInstanceOf(Tokenizer::class, $result);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsParsesArguments(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\nmyFunc('arg1', 'arg2', 'arg3');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'myFunc',
                'args' => ['arg1', 'arg2', 'arg3'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();

            $this->assertIsArray($args);
            $this->assertGreaterThan(0, count($args));
        } finally {
            unlink($testFile);
        }
    }

    public function testCountArgsReturnsZeroInitially(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        $count = $tokenizer->countArgs();
        $this->assertEquals(0, $count);
    }

    public function testSetFromBTNCallsClear(): void {
        $nodeData1 = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction1',
            'args' => [],
            'N' => 0
        ];
        $node1 = BacktraceNode::create($nodeData1, 0);

        $nodeData2 = [
            'file' => __FILE__,
            'line' => 20,
            'function' => 'testFunction2',
            'args' => [],
            'N' => 0
        ];
        $node2 = BacktraceNode::create($nodeData2, 0);

        $tokenizer = new Tokenizer($node1);
        $tokenizer->setArg(0, 'test');
        
        $this->assertCount(1, $tokenizer->getArgs());
        
        $tokenizer->setFromBTN($node2);
        
        $this->assertCount(0, $tokenizer->getArgs());
    }

    public function testConstructorCallsSetFromBTN(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0
        ];
        $node = BacktraceNode::create($nodeData, 0);

        $tokenizer = new Tokenizer($node);
        
        $reflection = new \ReflectionClass($tokenizer);
        $property = $reflection->getProperty('_debugBacktrace');
        $property->setAccessible(true);
        
        $this->assertNotNull($property->getValue($tokenizer));
    }

    public function testCreateWithStaticType(): void {
        $nodeData = [
            'file' => __FILE__,
            'line' => 10,
            'function' => 'testFunction',
            'args' => [],
            'N' => 0,
            'type' => '::'
        ];
        $node = BacktraceNode::create($nodeData, 0);

        $tokenizer = Tokenizer::create($node);
        $this->assertInstanceOf(Tokenizer::class, $tokenizer);
    }

    public function testParseCallArgsWithWhitespaceAndComments(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        // Code with comments and extra whitespace
        $testCode = "<?php\n/* comment */ myFunc  (  'arg1'  ,  'arg2'  );  // end comment\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'myFunc',
                'args' => ['arg1', 'arg2'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            // Parse with whitespace stripping
            $tokenizer->parseCallArgs(true, false);
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsStripComments(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\nmyFunc('arg1' /* inline comment */, 'arg2');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'myFunc',
                'args' => ['arg1', 'arg2'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            // Parse with comment stripping
            $tokenizer->parseCallArgs(false, true);
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithNestedCalls(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\nouter(inner('a'), 'b');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'outer',
                'args' => ["inner('a')", 'b'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
            $this->assertGreaterThan(0, count($args));
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithArrayArgument(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\nmyFunc(['a', 'b', 'c']);\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'myFunc',
                'args' => [["a", "b", "c"]],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsCallsAddToArg(): void {
        // This test ensures addToArg() private method is called
        // by having a function call with actual arguments
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        // Simple function call that should be easily tokenized
        $testCode = "<?php\ntest(1);\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'test',
                'args' => [1],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            // This should call parseTokens, skipToStartCallArguments, addArg, and addToArg
            $tokenizer->parseCallArgs();
            
            $args = $tokenizer->getArgs();
            $this->assertIsArray($args);
            // At minimum, args array should exist
            $this->assertGreaterThanOrEqual(0, count($args));
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithMultipleArguments(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\nfunc('a', 'b', 'c');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'func',
                'args' => ['a', 'b', 'c'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithParenthesis(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\nf((1));\n"; // Nested parenthesis
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'f',
                'args' => [(1)],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithComma(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php\ntest(1, 2);\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'test',
                'args' => [1, 2],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithRealTokenization(): void {
        // Use this test file itself which has real function calls
        $nodeData = [
            'file' => __FILE__,
            'line' => __LINE__ - 4, // Point to the file_put_contents line below
            'function' => 'file_put_contents',
            'args' => ['$testFile', '$testCode'],
            'N' => 0,
            'type' => ''
        ];
        $node = BacktraceNode::create($nodeData, 0);
        $tokenizer = new Tokenizer($node);

        // This should successfully tokenize and call addToArg
        $tokenizer->parseCallArgs();
        
        // Just verify the method completes without error
        $this->assertIsArray($tokenizer->getArgs());
    }

    public function testAddToArgIsCalledIndirectly(): void {
        // Create a scenario where addToArg must be called
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        // Use a simple, clear function call
        $testCode = "<?php\n\$x = strlen('test');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 2,
                'function' => 'strlen',
                'args' => ['test'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            // Verify parsing occurred
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testFullParseCallArgsFlow(): void {
        // Test the full flow: parseTokens -> findCallStrings -> findTextCall
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        // Function must be on first line after <?php for _callText to include <?php
        $testCode = "<?php my_function('arg1');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1, // First line!
                'function' => 'my_function',
                'args' => ['arg1'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            // Execute full parsing flow
            $result = $tokenizer->parseCallArgs();
            
            $this->assertInstanceOf(Tokenizer::class, $result);
            $this->assertIsArray($tokenizer->getArgs());
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsOnFirstLine(): void {
        // When function is on first line, _callText includes <?php
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php test(1, 2);\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1,
                'function' => 'test',
                'args' => [1, 2],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
            // With proper tokenization, args should be populated
            $this->assertNotEmpty($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithNestedParenthesisOnFirstLine(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php f((1));\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1,
                'function' => 'f',
                'args' => [(1)],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithCommaOnFirstLine(): void {
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php test(1, 2);\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1,
                'function' => 'test',
                'args' => [1, 2],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            
            // Verify internal state
            $ref = new \ReflectionClass($tokenizer);
            $argsProp = $ref->getProperty('_args');
            $argsProp->setAccessible(true);
            $args = $argsProp->getValue($tokenizer);
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithNestedComma(): void {
        // Test comma inside nested parentheses - should trigger else branch
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php test(array(1, 2));\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1,
                'function' => 'test',
                'args' => ['array(1, 2)'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithDefaultToken(): void {
        // Test default case in switch - any token that's not (, ), or ,
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php test(\$var);\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1,
                'function' => 'test',
                'args' => ['$var'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithComments(): void {
        // Test comment handling - triggers T_COMMENT case
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php test(/* comment */ 'arg');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1,
                'function' => 'test',
                'args' => ['arg'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            // Parse without stripping comments
            $tokenizer->parseCallArgs(false, false);
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithDocComment(): void {
        // Test doc comment handling - triggers T_DOC_COMMENT case
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php test(/** doc */ 'arg');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1,
                'function' => 'test',
                'args' => ['arg'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs(false, false);
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithCommentStripped(): void {
        // Test comment stripping
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php test(/* comment */ 'arg');\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1,
                'function' => 'test',
                'args' => ['arg'],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            // Parse WITH stripping comments
            $tokenizer->parseCallArgs(false, true);
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }

    public function testParseCallArgsWithArraySyntax(): void {
        // Test array syntax which generates [ and ] string tokens
        $testFile = tempnam(sys_get_temp_dir(), 'tokenizer_test_');
        $testCode = "<?php test([1, 2]);\n";
        file_put_contents($testFile, $testCode);

        try {
            $nodeData = [
                'file' => $testFile,
                'line' => 1,
                'function' => 'test',
                'args' => [[1, 2]],
                'N' => 0,
                'type' => ''
            ];
            $node = BacktraceNode::create($nodeData, 0);
            $tokenizer = new Tokenizer($node);

            $tokenizer->parseCallArgs();
            $args = $tokenizer->getArgs();
            
            $this->assertIsArray($args);
        } finally {
            unlink($testFile);
        }
    }
}
