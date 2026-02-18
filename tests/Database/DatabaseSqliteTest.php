<?php

/**
 * Test for DatabaseSqlite class.
 */

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseSqlite;
use Hubbitus\HuPHP\Database\DatabaseSettingsSqlite;
use Hubbitus\HuPHP\Exceptions\Database\DatabaseConnectErrorException;
use Hubbitus\HuPHP\Exceptions\Database\DatabaseQueryFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseSqlite
 */
class DatabaseSqliteTest extends TestCase
{
    public function testClassExtendsDatabase(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);
        $db = new DatabaseSqlite($settings);

        $this->assertInstanceOf(DatabaseSqlite::class, $db);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\Database', $db);
        $this->assertEquals('sqlite3', $db->db_type);
    }

    public function testDbConnectSuccess(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        $this->assertInstanceOf(DatabaseSqlite::class, $db);
        $this->assertInstanceOf(PDO::class, $db->db_link);
    }

    public function testDbConnectFailureThrowsException(): void
    {
        $this->expectException(DatabaseConnectErrorException::class);

        $settings = new DatabaseSettingsSqlite([
            'db_file' => '/invalid/path/to/database.db'
        ]);

        $db = new DatabaseSqlite($settings);
    }

    public function testDbSelectIsStub(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        $this->assertTrue(method_exists($db, 'db_select'));
        // Should not throw any exception
        $db->db_select();
    }

    public function testQuerySuccess(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John')");

        $result = $db->query('SELECT * FROM test');
        $this->assertInstanceOf(PDOStatement::class, $result);

        $row = $db->sql_fetch_assoc();
        $this->assertEquals([
            'id' => 1,
            'name' => 'John'
        ], $row);
    }

    public function testQueryFailureThrowsException(): void
    {
        $this->expectException(DatabaseQueryFailedException::class);

        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        $db->query('SELECT * FROM non_existent_table');
    }

    public function testQueryLimitMethod(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John'), ('Jane'), ('Doe')");

        $result = $db->query_limit('SELECT * FROM test', 0, 2);
        $this->assertInstanceOf(PDOStatement::class, $result);

        $rows = [];
        while ($row = $db->sql_fetch_assoc()) {
            $rows[] = $row;
        }

        $this->assertCount(2, $rows);
        $this->assertEquals('1', $rows[0]['id']);
        $this->assertEquals('2', $rows[1]['id']);
    }

    public function testToBlobMethod(): void
    {
        $db = new DatabaseSqlite(new DatabaseSettingsSqlite());

        $testString = "test string";
        $result = $db->ToBlob($testString);
        $this->assertEquals($testString, $result);
    }

    public function testSqlNextResultMethod(): void
    {
        $db = new DatabaseSqlite(new DatabaseSettingsSqlite());

        $this->assertTrue(method_exists($db, 'sql_next_result'));
        $this->assertTrue((new \ReflectionMethod($db, 'sql_next_result'))->isPublic());
    }

    public function testSqlEscapeStringMethod(): void
    {
        $db = new DatabaseSqlite(new DatabaseSettingsSqlite());

        $testString = "O'Connor";
        $result = $db->sql_escape_string($testString);
        $this->assertStringContainsString("O'Connor", $result);
    }

    public function testRowsTotalMethod(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John'), ('Jane'), ('Doe')");

        $db->query('SELECT * FROM test');
        $result = $db->rowsTotal();

        $this->assertEquals(3, $result);
    }

    public function testCollectDebugInfoMethod(): void
    {
        $db = new DatabaseSqlite(new DatabaseSettingsSqlite());

        $this->assertTrue(method_exists($db, 'collectDebugInfo'));
        $this->assertEquals(4, (new \ReflectionMethod($db, 'collectDebugInfo'))->getNumberOfParameters());
    }

    public function testSqlFetchFieldMethod(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John')");

        $db->query('SELECT * FROM test');
        $fieldMeta = $db->sql_fetch_field(0);

        $this->assertIsArray($fieldMeta);
        $this->assertArrayHasKey('name', $fieldMeta);
        $this->assertArrayHasKey('table', $fieldMeta);
    }

    public function testSqlFetchAssocMethod(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John')");

        $db->query('SELECT * FROM test');
        $row = $db->sql_fetch_assoc();

        $this->assertIsArray($row);
        $this->assertArrayHasKey('id', $row);
        $this->assertArrayHasKey('name', $row);
    }

    public function testSqlFetchRowMethod(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John')");

        $db->query('SELECT * FROM test');
        $row = $db->sql_fetch_row();

        $this->assertIsArray($row);
        $this->assertCount(2, $row);
    }

    public function testSqlFetchArrayMethod(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John')");

        $db->query('SELECT * FROM test');
        $row = $db->sql_fetch_array();

        $this->assertIsArray($row);
        $this->assertArrayHasKey(0, $row);
        $this->assertArrayHasKey('id', $row);
        $this->assertArrayHasKey('name', $row);
    }

    public function testSqlFetchObjectMethod(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John')");

        $db->query('SELECT * FROM test');
        $object = $db->sql_fetch_object();

        $this->assertInstanceOf(stdClass::class, $object);
        $this->assertObjectHasAttribute('id', $object);
        $this->assertObjectHasAttribute('name', $object);
    }

    public function testSqlFreeResultMethod(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John')");

        $db->query('SELECT * FROM test');
        $result = $db->sql_free_result();

        $this->assertTrue($result);
        $this->assertNull($db->result);
    }

    public function testSqlNumRowsMethod(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        // Create table and insert data
        $db->query('CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT)');
        $db->query("INSERT INTO test (name) VALUES ('John'), ('Jane'), ('Doe')");

        $db->query('SELECT * FROM test');
        $rowCount = $db->sql_num_rows();

        $this->assertEquals(3, $rowCount);
    }

    public function testSettingsInheritance(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:',
            'DEBUG' => true,
            'CHARSET_RECODE' => [
                'FROM' => 'UTF8',
                'TO' => 'ISO-8859-1'
            ]
        ]);

        $db = new DatabaseSqlite($settings);

        $this->assertEquals(':memory:', $db->settings->db_file);
        $this->assertTrue($db->settings->DEBUG);
        $this->assertEquals('UTF8', $db->settings->CHARSET_RECODE['FROM']);
        $this->assertEquals('ISO-8859-1', $db->settings->CHARSET_RECODE['TO']);
    }

    public function testConstructorParameters(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings, true);

        $this->assertInstanceOf(DatabaseSqlite::class, $db);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\Database', $db);
    }

    public function testDatabaseTypeProperty(): void
    {
        $db = new DatabaseSqlite(new DatabaseSettingsSqlite());

        $this->assertEquals('sqlite3', $db->db_type);
    }

    public function testErrorHandling(): void
    {
        $db = new DatabaseSqlite(new DatabaseSettingsSqlite());

        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\DatabaseError', $db->getError());
    }

    public function testMagicMethods(): void
    {
        $settings = new DatabaseSettingsSqlite([
            'db_file' => ':memory:'
        ]);

        $db = new DatabaseSqlite($settings);

        $this->assertEquals(':memory:', $db->settings->db_file);
    }

    public function testWakeupMethod(): void
    {
        $db = new DatabaseSqlite(new DatabaseSettingsSqlite());

        $this->assertTrue(method_exists($db, '__wakeup'));
    }
}