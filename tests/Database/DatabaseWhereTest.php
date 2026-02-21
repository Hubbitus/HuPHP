<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseWhere;
use PHPUnit\Framework\TestCase;

/**
 * @covers Hubbitus\HuPHP\Database\DatabaseWhere
 */
final class DatabaseWhereTest extends TestCase
{
    public function testConstructorCreatesEmptyWhere(): void {
        $where = new DatabaseWhere();
        
        $this->assertEmpty($where->getArray());
        $this->assertEquals(0, $where->count());
    }

    public function testConstructorWithArray(): void {
        $conditions = ['ID' => 1];
        $where = new DatabaseWhere($conditions);
        
        $this->assertEquals($conditions, $where->getArray());
        $this->assertEquals(1, $where->count());
    }

    public function testConstructorWithQuotes(): void {
        $conditions = ['ID' => 1];
        $where = new DatabaseWhere($conditions, '`', '`', "'");
        
        $this->assertEquals($conditions, $where->getArray());
    }

    public function testSetArray(): void {
        $where = new DatabaseWhere();
        $newConditions = ['name' => 'test'];
        
        $where->setArray($newConditions, '`', '`');
        
        $this->assertEquals($newConditions, $where->getArray());
        $this->assertEquals(1, $where->count());
    }

    public function testAddCondition(): void {
        $where = new DatabaseWhere(['ID' => 1]);
        $where->add(['status' => 'active']);
        
        $this->assertEquals(2, $where->count());
        $this->assertEquals(['ID' => 1, ['status' => 'active']], $where->getArray());
    }

    public function testAddStringCondition(): void {
        $where = new DatabaseWhere(['ID' => 1]);
        $where->add('AND');
        
        $this->assertEquals(2, $where->count());
    }

    public function testGetArrayReturnsOriginalArray(): void {
        $conditions = ['ID' => 1, 'name' => 'test'];
        $where = new DatabaseWhere($conditions);
        
        $this->assertEquals($conditions, $where->getArray());
    }

    public function testAppend(): void {
        $where1 = new DatabaseWhere(['ID' => 1]);
        $where2 = new DatabaseWhere(['status' => 'active']);
        
        $where1->append($where2);
        
        $this->assertEquals(2, $where1->count());
    }

    public function testAppendEmpty(): void {
        $where1 = new DatabaseWhere(['ID' => 1]);
        $where2 = new DatabaseWhere();
        
        $where1->append($where2);
        
        $this->assertEquals(1, $where1->count());
    }

    public function testSafeAppend(): void {
        $where1 = new DatabaseWhere(['ID' => 1]);
        $where2 = new DatabaseWhere(['status' => 'active']);
        
        $where1->safeAppend($where2);
        
        $this->assertStringContainsString('AND', $where1->getSQL());
        $this->assertEquals(3, $where1->count()); // Original + 'AND' + appended
    }

    public function testSafeAppendEmpty(): void {
        $where1 = new DatabaseWhere(['ID' => 1]);
        $where2 = new DatabaseWhere();
        
        $where1->safeAppend($where2);
        
        $this->assertEquals(1, $where1->count());
    }

    public function testCount(): void {
        $where = new DatabaseWhere(['ID' => 1, 'name' => 'test', 'status' => 'active']);
        
        $this->assertEquals(3, $where->count());
    }

    public function testGetSQLEmpty(): void {
        $where = new DatabaseWhere();
        
        $this->assertEquals('', $where->getSQL());
    }

    public function testGetSQLWithSimpleCondition(): void {
        $where = new DatabaseWhere(['ID' => 1]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('WHERE', $sql);
        $this->assertStringContainsString('ID', $sql);
        $this->assertStringContainsString('1', $sql);
    }

    public function testGetSQLWithMultipleConditions(): void {
        $where = new DatabaseWhere([
            'ID' => 1,
            'name' => 'test'
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('WHERE', $sql);
        $this->assertStringContainsString('ID', $sql);
        $this->assertStringContainsString('name', $sql);
    }

    public function testGetSQLWithOperator(): void {
        $where = new DatabaseWhere([
            ['age', 18, '>=']
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('>=', $sql);
        $this->assertStringContainsString('18', $sql);
    }

    public function testGetSQLWithBetweenOperator(): void {
        $where = new DatabaseWhere([
            ['age', 18, 'BETWEEN', 30]
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('BETWEEN', $sql);
        $this->assertStringContainsString('AND', $sql);
    }

    public function testGetSQLWithLogicalOperator(): void {
        $where = new DatabaseWhere([
            ['ID' => 1],
            'OR',
            ['status' => 'active']
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('OR', $sql);
    }

    public function testGetSQLWithQuotedValues(): void {
        $where = new DatabaseWhere([
            ['name', 'test', 'q:=']
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString("'test'", $sql);
    }

    public function testGetSQLWithEscapedFieldNames(): void {
        $where = new DatabaseWhere([
            ['name', 'test', 'e:=']
        ], '`', '`');
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('`name`', $sql);
    }

    public function testGetSQLWithArraySyntax(): void {
        $where = new DatabaseWhere([
            'ID' => [1, '<=']
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('<=', $sql);
        $this->assertStringContainsString('1', $sql);
    }

    public function testGetSQLCachesResult(): void {
        $where = new DatabaseWhere(['ID' => 1]);
        
        $sql1 = $where->getSQL();
        $sql2 = $where->getSQL();
        
        $this->assertEquals($sql1, $sql2);
    }

    public function testGetSQLAfterAddInvalidatesCache(): void {
        $where = new DatabaseWhere(['ID' => 1]);
        $sql1 = $where->getSQL();
        
        $where->add(['status' => 'active']);
        $sql2 = $where->getSQL();
        
        $this->assertNotEquals($sql1, $sql2);
    }

    public function testConstructPhraseWithDefaultOperator(): void {
        $where = new DatabaseWhere([
            ['name' => 'test']
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('=', $sql);
    }

    public function testConstructPhraseWithCustomOperator(): void {
        $where = new DatabaseWhere([
            ['name', 'test%', 'LIKE']
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('LIKE', $sql);
        $this->assertStringContainsString('test%', $sql);
    }

    public function testConstructPhraseWithQuotedOption(): void {
        $where = new DatabaseWhere([
            ['name', 'test', 'q:=']
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString("'test'", $sql);
    }

    public function testConstructPhraseWithEscapedOption(): void {
        $where = new DatabaseWhere([
            ['name', 'test', 'e:=']
        ], '[', ']');
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('[name]', $sql);
    }

    public function testConstructPhraseWithQuotedAndEscapedOption(): void {
        $where = new DatabaseWhere([
            ['name', 'test', 'qe:=']
        ], '[', ']');
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('[name]', $sql);
        $this->assertStringContainsString("'test'", $sql);
    }

    public function testGetSQLWithComplexConditions(): void {
        $where = new DatabaseWhere([
            ['ID' => 1],
            'AND',
            ['status' => 'active'],
            'OR',
            ['priority', 5, '>']
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('WHERE', $sql);
        $this->assertStringContainsString('AND', $sql);
        $this->assertStringContainsString('OR', $sql);
    }

    public function testGetSQLWithNumericStringCondition(): void {
        $where = new DatabaseWhere([
            ['ID' => 1],
            '1=1'
        ]);
        
        $sql = $where->getSQL();
        
        $this->assertStringContainsString('1=1', $sql);
    }

    public function testGetSQLWithSecondSyntax(): void {
        $where = new DatabaseWhere([
            'ID' => 1,
            'name' => ['test', 'LIKE']
        ]);

        $sql = $where->getSQL();

        $this->assertStringContainsString('ID', $sql);
        $this->assertStringContainsString('name', $sql);
        $this->assertStringContainsString('LIKE', $sql);
    }

    public function testEscapeFieldNameWithoutEscapeOption(): void {
        $where = new DatabaseWhere([
            ['name', 'test', ':=']
        ], '[', ']');

        $sql = $where->getSQL();

        $this->assertStringContainsString('name', $sql);
        $this->assertStringNotContainsString('[name]', $sql);
    }

    public function testQuoteFieldValueWithoutQuoteOption(): void {
        $where = new DatabaseWhere([
            ['ID' => 5]
        ]);

        $sql = $where->getSQL();

        $this->assertStringContainsString('ID', $sql);
        $this->assertStringContainsString('5', $sql);
    }

    public function testConvertToSQLEmptyArray(): void {
        $where = new DatabaseWhere([]);

        $sql = $where->getSQL();

        $this->assertEquals('', $sql);
    }

    public function testSetArrayResetsWhereStr(): void {
        $where = new DatabaseWhere(['ID' => 1]);
        $sql1 = $where->getSQL();

        $where->setArray(['name' => 'test']);
        $sql2 = $where->getSQL();

        $this->assertNotEquals($sql1, $sql2);
        $this->assertStringContainsString('name', $sql2);
    }

    public function testConstructPhraseWithDefaultOperatorImplicit(): void {
        $where = new DatabaseWhere([
            ['status', 'active']
        ]);

        $sql = $where->getSQL();

        $this->assertStringContainsString('=', $sql);
        $this->assertStringContainsString('status', $sql);
        $this->assertStringContainsString('active', $sql);
    }

    public function testGetSQLWithFirstSyntaxArrayValue(): void {
        // Case <4>: array('ID', array (2, '<='))
        $where = new DatabaseWhere([
            ['age', [30, '<=']]
        ]);

        $sql = $where->getSQL();

        $this->assertStringContainsString('age', $sql);
        $this->assertStringContainsString('<=', $sql);
        $this->assertStringContainsString('30', $sql);
    }

    public function testGetSQLWithSecondSyntaxSimpleArrayValue(): void {
        // Case <9>: 'USER' => array(5) - array without index 0
        $where = new DatabaseWhere([
            'status' => [5]
        ]);

        $sql = $where->getSQL();

        $this->assertStringContainsString('status', $sql);
        $this->assertStringContainsString('5', $sql);
    }
}
