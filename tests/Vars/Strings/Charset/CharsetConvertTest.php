<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Strings\Charset;

use Hubbitus\HuPHP\Vars\Strings\Charset\CharsetConvert;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use PHPUnit\Framework\TestCase;

/**
 * Mock implementation of abstract CharsetConvert for testing
 */
class MockCharsetConvert extends CharsetConvert {
    public function convert(): static {
        $this->_resText = \strtoupper($this->_text);
        return $this;
    }
}

/**
 * @covers \Hubbitus\HuPHP\Vars\Strings\Charset\CharsetConvert
 * @covers \Hubbitus\HuPHP\Vars\Strings\Charset\MockCharsetConvert
 */
class CharsetConvertTest extends TestCase {

    public function testConstructorWithAllParameters(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        
        $this->assertEquals('UTF-8', $converter->getInEnc());
        $this->assertEquals('UTF-8', $converter->getOutEnc());
        $this->assertEquals('hello', $converter->getText());
    }

    public function testConstructorWithDefaultOutEncoding(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8');
        
        $this->assertEquals('UTF-8', $converter->getInEnc());
        $this->assertEquals('UTF-8', $converter->getOutEnc());
    }

    public function testConstructorThrowsExceptionWithoutText(): void {
        $this->expectException(VariableRequiredException::class);
        
        new MockCharsetConvert(null, 'UTF-8', 'UTF-8');
    }

    public function testSetInEnc(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        $result = $converter->setInEnc('ISO-8859-1');
        
        $this->assertEquals('ISO-8859-1', $converter->getInEnc());
        $this->assertSame($converter, $result);
    }

    public function testSetInEncClearsResult(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        $converter->convert();
        
        // Set new encoding should clear result
        $converter->setInEnc('ISO-8859-1');
        
        // Access protected property via reflection to verify
        $reflection = new \ReflectionClass($converter);
        $resTextProp = $reflection->getProperty('_resText');
        $resTextProp->setAccessible(true);
        
        $this->assertNull($resTextProp->getValue($converter));
    }

    public function testGetInEnc(): void {
        $converter = new MockCharsetConvert('hello', 'ISO-8859-1', 'UTF-8');
        
        $this->assertEquals('ISO-8859-1', $converter->getInEnc());
    }

    public function testSetOutEnc(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        $result = $converter->setOutEnc('ISO-8859-1');
        
        $this->assertEquals('ISO-8859-1', $converter->getOutEnc());
        $this->assertSame($converter, $result);
    }

    public function testSetOutEncClearsResult(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        $converter->convert();
        
        // Set new encoding should clear result
        $converter->setOutEnc('ISO-8859-1');
        
        // Access protected property via reflection to verify
        $reflection = new \ReflectionClass($converter);
        $resTextProp = $reflection->getProperty('_resText');
        $resTextProp->setAccessible(true);
        
        $this->assertNull($resTextProp->getValue($converter));
    }

    public function testGetOutEnc(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'ISO-8859-1');
        
        $this->assertEquals('ISO-8859-1', $converter->getOutEnc());
    }

    public function testSetText(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        $result = $converter->setText('world');
        
        $this->assertEquals('world', $converter->getText());
        $this->assertSame($converter, $result);
    }

    public function testGetText(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        
        $this->assertEquals('hello', $converter->getText());
    }

    public function testGetResultCallsConvert(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        
        // getResult() should call convert() if _resText is empty
        $result = $converter->getResult();
        
        $this->assertEquals('HELLO', $result);
    }

    public function testGetResultReturnsCachedResult(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        
        // First call
        $result1 = $converter->getResult();
        
        // Modify text - result should still be cached
        $reflection = new \ReflectionClass($converter);
        $resTextProp = $reflection->getProperty('_resText');
        $resTextProp->setAccessible(true);
        $resTextProp->setValue($converter, 'CACHED');
        
        $result2 = $converter->getResult();
        
        $this->assertEquals('CACHED', $result2);
    }

    public function testToStringCallsGetResult(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        
        $result = (string) $converter;
        
        $this->assertEquals('HELLO', $result);
    }

    public function testStaticConv(): void {
        $result = MockCharsetConvert::conv('hello', 'UTF-8', 'UTF-8');
        
        $this->assertEquals('HELLO', $result);
    }

    public function testStaticConvWithDefaultOutEncoding(): void {
        $result = MockCharsetConvert::conv('hello', 'UTF-8');
        
        $this->assertEquals('HELLO', $result);
    }

    public function testConvertReturnsSelf(): void {
        $converter = new MockCharsetConvert('hello', 'UTF-8', 'UTF-8');
        $result = $converter->convert();
        
        $this->assertSame($converter, $result);
    }
}
