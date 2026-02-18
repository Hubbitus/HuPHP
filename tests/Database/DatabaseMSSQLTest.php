<?php

/**
 * Test for DatabaseMSSQL class.
 */

namespace Hubbitus\HuPHP\Tests\Database;

use Hubbitus\HuPHP\Database\DatabaseMSSQL;
use Hubbitus\HuPHP\Database\DatabaseSettingsMSSQL;
use Hubbitus\HuPHP\Exceptions\Database\DatabaseConnectErrorException;
use Hubbitus\HuPHP\Exceptions\Database\DatabaseQueryFailedException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Database\DatabaseMSSQL
 */
class DatabaseMSSQLTest extends TestCase
{
    public function testClassExtendsDatabase(): void
    {
        $settings = new DatabaseSettingsMSSQL();
        $db = new DatabaseMSSQL($settings);

        $this->assertInstanceOf(DatabaseMSSQL::class, $db);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\Database', $db);
        $this->assertEquals('mssql', $db->db_type);
    }

    public function testDbConnectSuccess(): void
    {
        // This would require actual MSSQL server connection
        // We'll test the method structure and error handling
        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'localhost',
            'username' => 'testuser',
            'password' => 'testpass',
            'dbName' => 'testdb'
        ]);

        $db = new DatabaseMSSQL($settings, true); // doNotConnect = true

        $this->assertInstanceOf(DatabaseMSSQL::class, $db);
        $this->assertNull($db->db_link);
    }

    public function testDbConnectFailureThrowsException(): void
    {
        $this->expectException(DatabaseConnectErrorException::class);

        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'invalid_host',
            'username' => 'invalid_user',
            'password' => 'invalid_pass',
            'dbName' => 'invalid_db'
        ]);

        $db = new DatabaseMSSQL($settings);
    }

    public function testQuerySuccess(): void
    {
        // This would require actual MSSQL server connection
        // We'll test the method structure and error handling
        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'localhost',
            'username' => 'testuser',
            'password' => 'testpass',
            'dbName' => 'testdb',
            'DEBUG' => false
        ]);

        $db = new DatabaseMSSQL($settings, true); // doNotConnect = true

        // Test that query method exists and has correct parameters
        $this->assertTrue(method_exists($db, 'query'));
        $this->assertEquals(3, (new \ReflectionMethod($db, 'query'))->getNumberOfParameters());
    }

    public function testQueryFailureThrowsException(): void
    {
        $this->expectException(DatabaseQueryFailedException::class);

        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'localhost',
            'username' => 'testuser',
            'password' => 'testpass',
            'dbName' => 'testdb',
            'DEBUG' => false
        ]);

        $db = new DatabaseMSSQL($settings, true); // doNotConnect = true
        $db->query('SELECT * FROM non_existent_table');
    }

    public function testQueryLimitMethod(): void
    {
        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'localhost',
            'username' => 'testuser',
            'password' => 'testpass',
            'dbName' => 'testdb',
            'DEBUG' => false
        ]);

        $db = new DatabaseMSSQL($settings, true); // doNotConnect = true

        $this->assertTrue(method_exists($db, 'query_limit'));
        $this->assertEquals(4, (new \ReflectionMethod($db, 'query_limit'))->getNumberOfParameters());
    }

    public function testToBlobMethod(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $testString = "test string";
        $expected = '0x7465737420737472696e67';

        $result = $db->ToBlob($testString);
        $this->assertEquals($expected, $result);
    }

    public function testSqlNextResultMethod(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $this->assertTrue(method_exists($db, 'sql_next_result'));
        $this->assertTrue((new \ReflectionMethod($db, 'sql_next_result'))->isFinal());
    }

    public function testSqlEscapeStringMethod(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $testString = "O'Connor";
        $expected = "O''Connor";

        $result = $db->sql_escape_string($testString);
        $this->assertEquals($expected, $result);
    }

    public function testMSSQLintArrayMethod(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $testArray = [1, 2, 3, 4, 5];
        $expected = '    1    2    3    4    5';

        $result = $db->MSSQLintArray($testArray);
        $this->assertEquals($expected, $result);
    }

    public function testIntFixedLengthHelperMethod(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $reflection = new \ReflectionClass(DatabaseMSSQL::class);
        $method = $reflection->getMethod('int_fixed_length');
        $method->setAccessible(true);

        $result = $method->invoke($db, 42);
        $this->assertEquals('   42', $result);
    }

    public function testSqlFetchObjectMethod(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $this->assertTrue(method_exists($db, 'sql_fetch_object'));
        $this->assertEquals(2, (new \ReflectionMethod($db, 'sql_fetch_object'))->getNumberOfParameters());
    }

    public function testCollectDebugInfoMethod(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $this->assertTrue(method_exists($db, 'collectDebugInfo'));
        $this->assertEquals(4, (new \ReflectionMethod($db, 'collectDebugInfo'))->getNumberOfParameters());
    }

    public function testSettingsInheritance(): void
    {
        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'localhost',
            'username' => 'testuser',
            'password' => 'testpass',
            'dbName' => 'testdb',
            'persistent' => true,
            'charset' => 'UTF8',
            'DEBUG' => true
        ]);

        $db = new DatabaseMSSQL($settings);

        $this->assertEquals('localhost', $db->settings->hostname);
        $this->assertEquals('testuser', $db->settings->username);
        $this->assertEquals('testpass', $db->settings->password);
        $this->assertEquals('testdb', $db->settings->dbName);
        $this->assertTrue($db->settings->persistent);
        $this->assertEquals('UTF8', $db->settings->charset);
        $this->assertTrue($db->settings->DEBUG);
    }

    public function testConstructorParameters(): void
    {
        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'localhost',
            'username' => 'testuser',
            'password' => 'testpass'
        ]);

        $db = new DatabaseMSSQL($settings, true);

        $this->assertInstanceOf(DatabaseMSSQL::class, $db);
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\Database', $db);
    }

    public function testDatabaseTypeProperty(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $this->assertEquals('mssql', $db->db_type);
    }

    public function testErrorHandling(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $this->assertInstanceOf('Hubbitus\\HuPHP\\Database\\DatabaseError', $db->getError());
    }

    public function testMagicMethods(): void
    {
        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'localhost'
        ]);

        $db = new DatabaseMSSQL($settings);

        $this->assertEquals('localhost', $db->settings->hostname);
    }

    public function testWakeupMethod(): void
    {
        $db = new DatabaseMSSQL(new DatabaseSettingsMSSQL());

        $this->assertTrue(method_exists($db, '__wakeup'));
    }

    public function testQueryLastId(): void
    {
        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'localhost',
            'username' => 'testuser',
            'password' => 'testpass',
            'dbName' => 'testdb',
            'DEBUG' => false
        ]);

        $db = new DatabaseMSSQL($settings, true); // doNotConnect = true

        $this->assertTrue(method_exists($db, 'query'));
        // This would require actual database connection to test properly
    }

    public function testQueryPrinting(): void
    {
        $settings = new DatabaseSettingsMSSQL([
            'hostname' => 'localhost',
            'username' => 'testuser',
            'password' => 'testpass',
            'dbName' => 'testdb',
            'DEBUG' => false
        ]);

        $db = new DatabaseMSSQL($settings, true); // doNotConnect = true

        $this->assertTrue(method_exists($db, 'query'));
        // This would require actual database connection to test properly
    }
}