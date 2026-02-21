<?php

/**
 * Test for DatabaseOperators class.
 */

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseOperators;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseOperators
 */
class DatabaseOperatorsTest extends TestCase {
    public function testClassPropertiesAreArrays(): void {
        $this->assertIsArray(DatabaseOperators::$operators1);
        $this->assertIsArray(DatabaseOperators::$operators2);
        $this->assertIsArray(DatabaseOperators::$operators3);
        $this->assertIsArray(DatabaseOperators::$operatorsLogical);
        $this->assertIsArray(DatabaseOperators::$operatorsFlow);
    }

    public function testUnaryOperators(): void {
        $expectedOperators1 = [
            'BINARY',
            'COLLATE',
            '~',
            '-' // Change the sign of the argument
        ];

        $this->assertEquals($expectedOperators1, DatabaseOperators::$operators1);
        $this->assertContains('~', DatabaseOperators::$operators1);
        $this->assertContains('-', DatabaseOperators::$operators1);
        $this->assertContains('BINARY', DatabaseOperators::$operators1);
    }

    public function testBinaryOperators(): void {
        $expectedOperators2 = [
            '>>',
            '*',
            '-' /*Minus operator */,
            'RLIKE',
            'SOUNDS LIKE',
            '&',
            '|',
            '^',
            'DIV',
            '/',
            '<=>',
            '=',
            '>=',
            '>',
            'IS NOT NULL',
            'IS NOT',
            'IS NULL',
            'IS',
            '<<',
            '<=',
            '<',
            'LIKE',
            '!=, <>',
            'NOT LIKE',
            'NOT REGEXP',
            'NOT, !',
            '%',
            '+',
            'REGEXP',
        ];

        $this->assertEquals($expectedOperators2, DatabaseOperators::$operators2);
        $this->assertContains('=', DatabaseOperators::$operators2);
        $this->assertContains('<<', DatabaseOperators::$operators2);
        $this->assertContains('LIKE', DatabaseOperators::$operators2);
        $this->assertContains('IS NOT NULL', DatabaseOperators::$operators2);
    }

    public function testTernaryOperators(): void {
        $expectedOperators3 = [
            'BETWEEN',
            'NOT BETWEEN' // '! BETWEEN' is incorrect!
        ];

        $this->assertEquals($expectedOperators3, DatabaseOperators::$operators3);
        $this->assertContains('BETWEEN', DatabaseOperators::$operators3);
        $this->assertContains('NOT BETWEEN', DatabaseOperators::$operators3);
    }

    public function testLogicalOperators(): void {
        $expectedOperatorsLogical = [
            'AND', '&&',
            'XOR',
            '||', 'OR'
        ];

        $this->assertEquals($expectedOperatorsLogical, DatabaseOperators::$operatorsLogical);
        $this->assertContains('AND', DatabaseOperators::$operatorsLogical);
        $this->assertContains('OR', DatabaseOperators::$operatorsLogical);
        $this->assertContains('XOR', DatabaseOperators::$operatorsLogical);
    }

    public function testFlowOperators(): void {
        $expectedOperatorsFlow = [
            'CASE'
        ];

        $this->assertEquals($expectedOperatorsFlow, DatabaseOperators::$operatorsFlow);
        $this->assertContains('CASE', DatabaseOperators::$operatorsFlow);
    }

    public function testOperatorCategoriesAreDistinct(): void {
        // Ensure no operator is duplicated across categories
        $allOperators = array_merge(
            DatabaseOperators::$operators1,
            DatabaseOperators::$operators2,
            DatabaseOperators::$operators3,
            DatabaseOperators::$operatorsLogical,
            DatabaseOperators::$operatorsFlow
        );

        $uniqueOperators = array_unique($allOperators);
        $this->assertEquals(count($allOperators), count($uniqueOperators));
    }

    public function testOperatorArrayCounts(): void {
        $this->assertCount(4, DatabaseOperators::$operators1);
        $this->assertCount(24, DatabaseOperators::$operators2);
        $this->assertCount(2, DatabaseOperators::$operators3);
        $this->assertCount(4, DatabaseOperators::$operatorsLogical);
        $this->assertCount(1, DatabaseOperators::$operatorsFlow);
    }

    public function testOperatorCaseSensitivity(): void {
        // Test that operators are case-sensitive as expected
        $this->assertNotContains('and', DatabaseOperators::$operatorsLogical);
        $this->assertNotContains('or', DatabaseOperators::$operatorsLogical);
        $this->assertNotContains('is null', DatabaseOperators::$operators2);
    }

    public function testOperatorSyntax(): void {
        // Test that operators have correct syntax
        $this->assertStringContainsString('<<', DatabaseOperators::$operators2[34]);
        $this->assertStringContainsString('<=', DatabaseOperators::$operators2[35]);
        $this->assertStringContainsString('<', DatabaseOperators::$operators2[36]);
        $this->assertStringContainsString('=', DatabaseOperators::$operators2[28]);
    }

    public function testOperatorComments(): void {
        // Test that operators have appropriate comments where needed
        $this->assertStringContainsString('Change the sign of the argument', DatabaseOperators::$operators1[3]);
        $this->assertStringContainsString('Minus operator', DatabaseOperators::$operators2[3]);
        $this->assertStringContainsString('"! BETWEEN" is incorrect!', DatabaseOperators::$operators3[1]);
    }

    public function testOperatorArrayIsStatic(): void {
        // Test that operators are static properties
        $this->assertTrue(defined('Hubbitus\\HuPHP\\Database\\DatabaseOperators::$operators1'));
        $this->assertTrue(defined('Hubbitus\\HuPHP\\Database\\DatabaseOperators::$operators2'));
        $this->assertTrue(defined('Hubbitus\\HuPHP\\Database\\DatabaseOperators::$operators3'));
        $this->assertTrue(defined('Hubbitus\\HuPHP\\Database\\DatabaseOperators::$operatorsLogical'));
        $this->assertTrue(defined('Hubbitus\\HuPHP\\Database\\DatabaseOperators::$operatorsFlow'));
    }
}