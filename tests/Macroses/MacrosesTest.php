<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Macroses;

use Hubbitus\HuPHP\Exceptions\Variables\VariableIsNullException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class MacrosesTest extends TestCase {
    
    public function testRequiredNotNullExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\REQUIRED_NOT_NULL'));
    }

    public function testRequiredNotNullReturnsValueWhenNotNull(): void {
        $value = 'test';
        $result = \Hubbitus\HuPHP\Macroses\REQUIRED_NOT_NULL($value);
        
        $this->assertSame('test', $result);
    }

    public function testRequiredNotNullThrowsExceptionWhenNull(): void {
        $value = null;
        
        $this->expectException(VariableIsNullException::class);
        \Hubbitus\HuPHP\Macroses\REQUIRED_NOT_NULL($value);
    }

    public function testRequiredVarExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\REQUIRED_VAR'));
    }

    public function testRequiredVarReturnsValueWhenNotNull(): void {
        $value = 'test';
        $result = \Hubbitus\HuPHP\Macroses\REQUIRED_VAR($value);
        
        $this->assertSame('test', $result);
    }

    public function testRequiredVarThrowsExceptionWhenNull(): void {
        $value = null;
        
        $this->expectException(VariableRequiredException::class);
        \Hubbitus\HuPHP\Macroses\REQUIRED_VAR($value);
    }

    public function testEmptyIntExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\EMPTY_INT'));
    }

    public function testEmptyStrExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\EMPTY_STR'));
    }

    public function testEmptyVarExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\EMPTY_VAR'));
    }

    public function testNonEmptyStrExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\NON_EMPTY_STR'));
    }

    public function testIsSetExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\is_set'));
    }

    public function testIssetVarExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\ISSET_VAR'));
    }

    public function testIssetVarReturnsValueWhenSet(): void {
        $value = 'test';
        $result = \Hubbitus\HuPHP\Macroses\ISSET_VAR($value);
        
        $this->assertSame('test', $result);
    }

    public function testAssignIfExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\ASSIGN_IF'));
    }

    public function testSwapExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\SWAP'));
    }

    public function testSwapExchangesValues(): void {
        $a = 'first';
        $b = 'second';
        
        \Hubbitus\HuPHP\Macroses\SWAP($a, $b);
        
        $this->assertEquals('second', $a);
        $this->assertEquals('first', $b);
    }

    public function testEechoExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\eecho'));
    }

    public function testEechoOutputsToStderr(): void {
        $result = \Hubbitus\HuPHP\Macroses\eecho('test output');
        
        $this->assertGreaterThan(0, $result);
    }
}
