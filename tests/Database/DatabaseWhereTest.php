<?php

/**
 * Test for DatabaseWhere class.
 */

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseWhere;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseWhere
 */
class DatabaseWhereTest extends TestCase
{
    public function testClassPropertiesAndConstants(): void
    {
        $where = new DatabaseWhere();

        $this->assertIsArray($where->_getArray());
        $this->assertIsString($where->getSQL());
        $this->assertEquals('=', DatabaseWhere::default_operator);
    }

    public function testConstructorWithDefaultParameters(): void
    {
        $where = new DatabaseWhere();

        $this->assertEmpty($where->getArray());
        $this->assertEquals('', $where->getSQL());
        $this->assertEquals('', $where->_escapeFieldName('test_field', ''));
        $this->assertEquals('', $where->_quoteFieldValue('test_value', ''));
    }

    public function testConstructorWithArrayParameters(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1,
            'Name' => ['John', 'LIKE'],
            'Age' => ["25", ">="],
            'Status' => ['Active', 'q:LIKE']
        ]);

        $this->assertEquals('WHERE ((ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE ' . chr(39) . 'John' . chr(39) . ' AND Age>=' . chr(39) . '25' . chr(39) . ' AND Status LIKE ' . chr(39) . 'Active' . chr(39) . '))', $where->getSQL());
    }

    public function testConstructorWithBracketsParameters(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1,
            'Name' => ['John', 'LIKE'],
            '[',
            'Age' => ["25", ">="],
            ']'
        ], '[', ']');

        $this->assertEquals('WHERE ((ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE ' . chr(39) . 'John' . chr(39) . ' AND [Age]>=' . chr(39) . '25' . chr(39) . '))', $where->getSQL());
    }

    public function testSetArrayMethod(): void
    {
        $where = new DatabaseWhere();
        $where->setArray([
            'ID' => 1,
            'Name' => ['John', 'LIKE']
        ], '[', ']', '"');

        $expectedSql = 'WHERE (ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE "John")';
        $this->assertEquals($expectedSql, $where->getSQL());
    }

    public function testAddMethod(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1
        ]);

        $where->add([
            'Name' => ['John', 'LIKE']
        ]);

        $this->assertEquals('WHERE ((ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE ' . chr(39) . 'John' . chr(39) . '))', $where->getSQL());

        $where->add('AND');
        $where->add([
            'Age' => ["25", ">="]
        ]);

        $this->assertEquals('WHERE (ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE ' . chr(39) . 'John' . chr(39) . ' AND Age>=' . chr(39) . '25' . chr(39) . ')', $where->getSQL());
    }

    public function testAppendMethod(): void
    {
        $where1 = new DatabaseWhere([
            'ID' => 1
        ]);

        $where2 = new DatabaseWhere([
            'Name' => ['John', 'LIKE'],
            'Age' => ["25", ">="]
        ]);

        $where1->append($where2);

        $expectedSql = 'WHERE (ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE ' . chr(39) . 'John' . chr(39) . ' AND Age>="25")';
        $this->assertEquals($expectedSql, $where1->getSQL());
    }

    public function testSafeAppendMethod(): void
    {
        $where1 = new DatabaseWhere([
            'ID' => 1
        ]);

        $where2 = new DatabaseWhere([
            'Name' => ['John', 'LIKE'],
            'Age' => ["25", ">="]
        ]);

        $where1->safeAppend($where2);

        $expectedSql = 'WHERE (ID=' . chr(39) . '1' . chr(39) . ' AND (Name LIKE ' . chr(39) . 'John' . chr(39) . ' AND Age>=\\"25\\"))';
        $this->assertEquals($expectedSql, $where1->getSQL());
    }

    public function testCountMethod(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1,
            'Name' => ['John', 'LIKE']
        ]);

        $this->assertEquals(2, $where->count());

        $where->add([
            'Age' => ['25', '>=']
        ]);

        $this->assertEquals(3, $where->count());
    }

    public function testGetArrayMethod(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1,
            'Name' => ['John', 'LIKE']
        ]);

        $array = $where->getArray();
        $this->assertIsArray($array);
        $this->assertCount(2, $array);
        $this->assertEquals(1, $array[0]['ID']);
        $this->assertEquals(['John', 'LIKE'], $array[1]['Name']);
    }

    public function testGetSQLMethod(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1,
            'Name' => ['John', 'LIKE'],
            'Age' => ['25', '>=']
        ]);

        $this->assertEquals('WHERE (ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE ' . chr(39) . 'John' . chr(39) . ' AND Age>=' . chr(39) . '25' . chr(39) . ')', $where->getSQL());

        // Test that SQL is cached
        $where->add([
            'Status' => ['Active', '=']
        ]);

        $this->assertNotEquals('WHERE (ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE ' . chr(39) . 'John' . chr(39) . ' AND Age>=' . chr(39) . '25' . chr(39) . ')', $where->getSQL());
    }

    public function testConvertToSQLMethod(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1,
            'Name' => ['John', 'LIKE']
        ]);

        $reflection = new \ReflectionClass(DatabaseWhere::class);
        $method = $reflection->getMethod('convertToSQL');
        $method->setAccessible(true);
        $method->invoke($where);

        $this->assertEquals('WHERE (ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE ' . chr(39) . 'John' . chr(39) . ')', $where->_getWhereStr());
    }

    public function testConstructPhraseMethod(): void
    {
        $where = new DatabaseWhere();

        $reflection = new \ReflectionClass(DatabaseWhere::class);
        $method = $reflection->getMethod('constructPhrase');
        $method->setAccessible(true);

        $result = $method->invoke($where, 'ID', [1]);
        $this->assertEquals('ID=1', $result);

        $result = $method->invoke($where, 'Name', ['John', 'LIKE']);
        $this->assertEquals('Name LIKE ' . chr(39) . 'John' . chr(39), $result);

        $result = $method->invoke($where, 'Age', ['25', '>=']);
        $this->assertEquals('Age>=' . chr(39) . '25' . chr(39), $result);

        $result = $method->invoke($where, 'Date', ['q:BETWEEN', chr(39) . '2023-01-01' . chr(39), chr(39) . '2023-12-31' . chr(39)]);
        $this->assertEquals("Date BETWEEN '2023-01-01' AND '2023-12-31'", $result);
    }

    public function testEscapeFieldNameMethod(): void
    {
        $where = new DatabaseWhere();

        $reflection = new \ReflectionClass(DatabaseWhere::class);
        $method = $reflection->getMethod('escapeFieldName');
        $method->setAccessible(true);

        $result = $method->invoke($where, 'ID', '');
        $this->assertEquals('ID', $result);

        $result = $method->invoke($where, 'Name', 'e');
        $this->assertEquals('Name', $result);

        $where->setArray([], '[', ']');
        $result = $method->invoke($where, 'Name', 'e');
        $this->assertEquals('[Name]', $result);
    }

    public function testQuoteFieldValueMethod(): void
    {
        $where = new DatabaseWhere();

        $reflection = new \ReflectionClass(DatabaseWhere::class);
        $method = $reflection->getMethod('quoteFieldValue');
        $method->setAccessible(true);

        $result = $method->invoke($where, '1', '');
        $this->assertEquals('1', $result);

        $result = $method->invoke($where, 'John', 'q');
        $this->assertEquals(chr(39) . 'John' . chr(39), $result);

        $where->setArray([], '', '', '"');
        $result = $method->invoke($where, 'John', 'q');
        $this->assertEquals('"John"', $result);
    }

    public function testComplexWhereConditions(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1,
            'Name' => ['John', 'LIKE'],
            'Age' => ['25', '>='],
            'AND',
            [
                'Status' => ['Active', '='],
                'Date' => ['q:BETWEEN', "'2023-01-01'", "'2023-12-31'"]
            ]
        ]);

        $expectedSql = 'WHERE (ID=' . chr(39) . '1' . chr(39) . ' AND Name LIKE ' . chr(39) . 'John' . chr(39) . ' AND Age>="25\' AND Status = ' . chr(39) . 'Active' . chr(39) . ' AND Date BETWEEN ' . chr(39) . '2023-01-01' . chr(39) . ' AND ' . chr(39) . '2023-12-31';
        $this->assertEquals($expectedSql, $where->getSQL());
    }

    public function testEmptyWhereConditions(): void
    {
        $where = new DatabaseWhere();

        $this->assertEquals('', $where->getSQL());
        $this->assertEmpty($where->getArray());
        $this->assertEquals(0, $where->count());
    }

    public function testSingleCondition(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1
        ]);

        $this->assertEquals('WHERE ((ID=' . chr(39) . '1' . chr(39) . '))', $where->getSQL());
    }

    public function testMultipleOperators(): void
    {
        $where = new DatabaseWhere([
            'ID' => 1,
            'OR',
            'Name' => ['John', 'LIKE'],
            'AND',
            'Age' => ['25', '>=']
        ]);

        $expectedSql = 'WHERE (ID=' . chr(39) . '1' . chr(39) . ' OR Name LIKE ' . chr(39) . 'John' . chr(39) . ' AND Age>="25")';
        $this->assertEquals($expectedSql, $where->getSQL());
    }
}
